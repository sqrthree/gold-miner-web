<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
    /**
     * 获取所有翻译文章记录（分页）
     * @param   int $status 推荐文章记录类别，0 为未处理，1 为成功，2 为失败
     * @return  json_encode(Object) 全部推荐文章（分页）
     */
    public function index(Request $request)
    {
        //
        if (intval($request->input('status')) > 4) {
            header("HTTP/1.1 400 Bad request!");
            echo json_encode(['message' => '参数错误！']);
            return;
        }

        $translations = DB::table('translation')
            ->join('recommend', 'translation.rid', '=', 'recommend.id')
            ->join('category', 'category.id', '=', 'recommend.category')
            ->select('translation.*', 'category.category', 'recommend.title as originalTitle', 'recommend.url as originalUrl', 'recommend.description', 'recommend.recommender')
            ->where('translation.status', $request->has('status') ? intval($request->input('status')) : 0)
            ->orderBy('translation.udate', 'DESC')
            ->skip($this->start)
            ->take($this->offset)
            ->get();

        $involvedUsers = array();
        foreach ($translations as $k => $v) {
            $translations[$k]->reviewer = explode(',', trim($v->reviewer, ','));
            $involvedUsers[] = $translations[$k]->reviewer[0];
            $involvedUsers[] = $translations[$k]->reviewer[1];
            $involvedUsers[] = $v->translator;
        }
        $involvedUsers = array_unique($involvedUsers);

        $users = DB::table('user')->select('id', 'name', 'avatar')->whereIn('id', $involvedUsers)->get();

        $userList = array();
        foreach ($users as $k => $v) {
            $userList[$v->id] = (array) $v;
        }

        foreach ($translations as $k => $v) {
            $translations[$k]->translator = $userList[$v->translator];
            $translations[$k]->reviewer[0] = $userList[$v->reviewer[0]];
            $translations[$k]->reviewer[1] = $userList[$v->reviewer[1]];
            $translations[$k]->recommender = $userList[$v->recommender];
        }

        echo json_encode($translations);
    }

    /**
     * 处理 PR 相关的 WebHooks 请求
     * @param  Request $request WebHooks 请求内容
     * @return void
     */
    public function handlePR(Request $request)
    {
        $payload = $request->input('payload');
        // 处理 GitHub Repo 管理员发起的 PR
        // PR被 merge 时更新文章为待认领状态
        if ($payload->pull_request->user->login == env('GITHUB_ADMIN_USERNAME')) {
            if ($payload->action == 'closed' || $payload->pull_request->merged != false) {
                $result = $this->requestTranslate($request);

                if ($result == false) {
                    header('HTTP/1.1 503 Service unavailable!');
                    echo json_encode(['message' => '修改文章状态失败！']);
                    return;
                }
            }
        // 处理其他人发起的 PR
        } else {
            // PR 被 merge 时更新文章为发布（翻译完成）状态
            if ($payload->action == 'closed' || $payload->pull_request->merged != false) {
                $result = $this->requestPost($request);

                if ($result == false) {
                    header('HTTP/1.1 503 Service unavailable!');
                    echo json_encode(['message' => '修改文章状态失败！']);
                    return;
                }
            // PR 被创建时更新文章为待校对状态
            } elseif ($payload->action = 'opened') {
                $result = $this->requestReview($request);

                if ($result == false) {
                    header('HTTP/1.1 503 Service unavailable!');
                    echo json_encode(['message' => '修改文章状态失败！']);
                    return;
                }
            }
        }

    }

    /**
     * 添加一篇翻译文章，状态为抓取中
     * @param  int      rid          该文章在推荐表中的记录 ID （唯一）
     * @param  string   file         文件名 （唯一）
     * @param  int      tduration    翻译时长
     * @param  int      rduration    校对时长
     * @param  int      tscore       翻译积分
     * @param  int      rscore       校对积分
     * @param  int      word         文章字数
     * @return void
     */
    public function store(Request $request)
    {
        //
        $this->isNotNull(array(
            "ID" => $request->input('rid'),
            "GitHub 文件名" => $request->input('file'),
            "翻译时间" => $request->input('tduration'),
            "校对时间" => $request->input('rduration'),
            "翻译积分" => $request->input('tscore'),
            "校对积分" => $request->input('rscore'),
            "词量" => $request->input('word'),
        ));

        $this->isUnique('translation', array(
            'rid' => $request->input('rid'),
            'file' => $request->input('file'),
        ));

        $data = array(
            'rid' => $request->input('rid'),
            'file' => $request->input('file'),
            'tscore' => $request->input('tscore'),
            'rscore' => $request->input('rscore'),
            'word' => $request->input('word'),
            'tduration' => $request->input('tduration'),
            'rduration' => $request->input('rduration'),
            'status' => -1,
            'udate' => date("Y-m-d H:i:s"),
            'cdate' => date("Y-m-d H:i:s"),
        );

        $result = DB::table('translation')
            ->insert($data);

        if ($result == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '添加文章失败！']);
            return;
        }
    }

    /**
     * 请求翻译 （修改文章为待认领状态）
     * @param  Request $request PR 触发的 WebHooks 请求
     * @return boolean
     */
    public function requestTranslate(Request $request)
    {
        $file = $this->getPRFile($request);
        $data = array(
            'status' => 0,
            'udate' => date('Y-m-d H:i:s'),
        );

        return $this->updateByFile($file, $data);
    }

    /**
     * 认领翻译
     * @param   int     id       文章 ID
     * @param   int     uid      用户 ID
     * @param   string  username 用户名
     * @return  void
     */
    public function claimTranslation(Request $request)
    {
        $this->isNotNull(array(
            '文章 ID' => $request->input('id'),
            '译者 ID' => $request->input('uid'),
            '译者名'  => $request->input('username'),
        ));

        $timeline = array(
            array(
                'user'   => $request->input('username'),
                'uid'    => $request->input('uid'),
                'action' => '认领翻译',
                'time'   => date('Y-m-d H:i:s'),
            ),
        );

        $data = array(
            'translator' => $request->input('uid'),
            'timeline'   => json_encode($timeline),
            'status'     => 1,
        );

        $result = DB::table('translation')
            ->where('id', $request->input('id'))
            ->update($data);

        if ($result == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '认领失败！']);
            return;
        }
    }

    /**
     * 请求校对 （修改文章为待校对状态）
     * @param  Request $request PR 触发的 WebHooks 请求
     * @return boolean
     */
    public function requestReview(Request $request)
    {
        $file = $this->getPRFile($request);
        $data = array(
            'pr' => $this->getPR($request)->id,
            'status' => 2,
            'udate' => date('Y-m-d H:i:s'),
        );

        return $this->updateByFile($file, $data);
    }

    /**
     * 认领校对
     * @param  int   id         文章 ID
     * @param  int   uid        用户 ID
     * @param  int   username   用户名
     * @return 
     */
    public function claimReview(Request $request)
    {
        $this->isNotNull(array(
            '文章 ID' => $request->input('id'),
            '译者 ID' => $request->input('uid'),
            '译者名' => $request->input('username'),
        ));

        $record = DB::table('translation')
            ->select('reviewer', 'timeline')
            ->where('id', $request->input('id'))
            ->first();

        $reviewer = $record->reviewer;
        $timeline = json_decode($record->timeline);
        array_push($timeline, array(
            'user' => $request->input('username'),
            'uid' => $request->input('uid'),
            'action' => '认领校对',
            'time' => date('Y-m-d H:i:s'),
        ));

        $data = array(
            'reviewer' => $reviewer . ',' . $request->input('uid'),
            'status' => $reviewer ? 3 : 2,
            'timeline' => json_encode($timeline),
        );

        $result = DB::table('translation')
            ->where('id', $request->input('id'))
            ->update($data);

        if ($result == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '认领失败！']);
            return;
        }
    }

    /**
     * 请求发布 （修改文章为翻译完成状态）
     * @param  Request $request PR 触发的 WebHooks 请求
     * @return boolean
     */
    public function requestPost(Request $request)
    {
        $file = $this->getPRFile($request);
        $data = array(
            'title' => $this->getPR($request)->title,
            'status' => 4,
            'udate' => date('Y-m-d H:i:s'),
        );

        return $this->updateByFile($file, $data);
    }

    /**
     * 根据文件名修改文件信息
     * @param  string   $file   文件名
     * @param  array    $data   修改的字段及内容
     * @return boolean
     */
    public function updateByFile($file, $data)
    {
        return DB::table('translation')
            ->where('file', $file)
            ->update($data);
    }

    /**
     * 获取 PR 中被修改文件的文件名
     * @param  Request  $request PR 触发的 WebHooks 中的请求内容
     * @return string   修改的文件名 
     */
    public function getPRFile(Request $request)
    {
        $diff_url = $request->input('payload')->pull_request->diff_url;
        preg_match("/^diff --git a\/(.*?) b\/(.*?)\n/", $this->sendRequest($diff_url, 'GET'), $file);

        return $file[1];
    }

    /**
     * 获取 WebHooks 请求中 PR 的信息
     * @param  Request  $request    PR 触发的 WebHooks 请求内容
     * @return object   PR 信息
     */
    public function getPR(Request $request)
    {
        return $request->input('payload')->pull_request;
    }

}
