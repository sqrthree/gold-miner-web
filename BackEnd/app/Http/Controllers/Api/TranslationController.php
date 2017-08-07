<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TranslationController extends Controller
{
    /**
     * 获取所有翻译文章记录（分页）
     * @param   string $status 文章类别，awaiting 为待翻译或待校对，progressing 为正在翻译或正在校对，posted 为已发布
     * @return  json_encode(Object) 翻译文章（分页）
     */
    public function index($status)
    {
        //
        switch ($status) {
            case 'awaiting':
                $translations = DB::table('translation')
                                ->join('recommend', 'translation.rid', '=', 'recommend.id')
                                ->join('category', 'recommend.category', '=', 'category.id')
                                ->select('translation.id', 'translation.file', 'translation.title', 'translation.description', 'translation.link', 'translation.poster', 'translation.tscore', 'translation.rscore', 'translation.tduration', 'translation.rduration', 'translation.word', 'translation.translator', 'translation.reviewer1', 'translation.reviewer2', 'translation.pr', 'translation.status', 'category.category', 'recommend.title as oTitle', 'recommend.url as oUrl', 'recommend.recommender', 'recommend.description as oDescription')
                                ->where('translation.status', '0')
                                ->orWhere('translation.status', '2')
                                ->orderBy('translation.udate', 'ASC')
                                ->get();
                break;

            case 'progressing':
                $translations = DB::table('translation')
                                ->join('recommend', 'translation.rid', '=', 'recommend.id')
                                ->join('category', 'recommend.category', '=', 'category.id')
                                ->select('translation.id', 'translation.file', 'translation.title', 'translation.description', 'translation.link', 'translation.poster', 'translation.tscore', 'translation.rscore', 'translation.tduration', 'translation.rduration', 'translation.word', 'translation.translator', 'translation.reviewer1', 'translation.reviewer2', 'translation.pr', 'translation.status', 'category.category', 'recommend.title as oTitle', 'recommend.url as oUrl', 'recommend.recommender', 'recommend.description as oDescription')
                                ->where('translation.status', '1')
                                ->orWhere('translation.status', '3')
                                ->orderBy('translation.udate', 'ASC')
                                ->get();
                break;
            
            case 'posted':
                $translations = DB::table('translation')
                                ->join('recommend', 'translation.rid', '=', 'recommend.id')
                                ->join('category', 'recommend.category', '=', 'category.id')
                                ->select('translation.id', 'translation.file', 'translation.title', 'translation.description', 'translation.link', 'translation.poster', 'translation.tscore', 'translation.rscore', 'translation.tduration', 'translation.rduration', 'translation.word', 'translation.translator', 'translation.reviewer1', 'translation.reviewer2', 'translation.pr', 'translation.status', 'category.category', 'recommend.title as oTitle', 'recommend.url as oUrl', 'recommend.recommender', 'recommend.description as oDescription')
                                ->where('translation.status', '4')
                                ->orderBy('translation.udate', 'ASC')
                                ->get();
                break;
            
            default:
                header("HTTP/1.1 400 Bad request!");
                echo json_encode(['message' => '参数错误！']);
                return;
        }

        $involedUsers = array();
        foreach ($translations as $v) {
            $involedUsers[] = $v->translator;
            $involedUsers[] = $v->reviewer1;
            $involedUsers[] = $v->reviewer2;
            $involedUsers[] = $v->recommender;
        }

        $involedUsers = array_unique($involedUsers);
        
        $userList = DB::table('user')
                    ->select('id', 'name', 'avatar')
                    ->whereIn('id', $involedUsers)
                    ->get();

        $user = array(null);
        foreach ($userList as $u) {
            $user[$u->id] = $u;
        }

        foreach ($translations as $k => $t) {
            $translations[$k]->translator  = $user[$t->translator];
            $translations[$k]->reviewer1   = $user[$t->reviewer1];
            $translations[$k]->reviewer2   = $user[$t->reviewer2];
            $translations[$k]->recommender = $user[$t->recommender];
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
            "推荐 ID"  => $request->input('rid'),
            "文件名"   => $request->input('file'),
            "封面图"   => $request->input('poster'),
            "翻译时间" => $request->input('tduration'),
            "校对时间" => $request->input('rduration'),
            "翻译积分" => $request->input('tscore'),
            "校对积分" => $request->input('rscore'),
            "单词数量" => $request->input('word'),
        ));

        $this->isUnique('translation', array(
            'rid'   => $request->input('rid'),
            'file'  => $request->input('file'),
        ));

        $data = array(
            'rid'       => $request->input('rid'),
            'file'      => $request->input('file'),
            'poster'    => $request->input('poster'),
            'tscore'    => $request->input('tscore'),
            'rscore'    => $request->input('rscore'),
            'word'      => $request->input('word'),
            'tduration' => $request->input('tduration'),
            'rduration' => $request->input('rduration'),
            'status'    => -1,
            'udate'     => date("Y-m-d H:i:s"),
            'cdate'     => date("Y-m-d H:i:s"),
        );

        $translationId = DB::table('translation')
            ->insertGetId($data);

        if ($translationId == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '添加文章失败！']);
            return;
        }

        $recommender = DB::table('recommend')
                        ->where('id', $request->input('rid'))
                        ->value('recommender');

        LogController::writeTimeline(array(
            'tid'       => $translationId,
            'uid'       => $recommender,
            'operation' => "推荐成功",
            'cdate'     => date("Y-m-d H:i:s")
        ));
    }

    /**
     * 获取指定翻译记录详情
     * @param  int $id 记录 ID
     * @return object  翻译记录详情
     */
    public function show($id)
    {
        if (!is_numeric($id)) {
            header("HTTP/1.1 400 Bad request");
            echo json_encode(['message' => '参数错误！']);
            return;
        }

        $translation = DB::table('translation')
                        ->join('recommend', 'translation.rid', '=', 'recommend.id')
                        ->join('category', 'recommend.category', '=', 'category.id')
                        ->select('translation.id', 'translation.file', 'translation.title', 'translation.description', 'translation.link', 'translation.poster', 'translation.tscore', 'translation.rscore', 'translation.tduration', 'translation.rduration', 'translation.word', 'translation.translator', 'translation.reviewer1', 'translation.reviewer2', 'translation.pr', 'translation.status', 'category.category', 'recommend.title as oTitle', 'recommend.url as oUrl', 'recommend.recommender', 'recommend.description as oDescription')
                        ->where('translation.id', $id)
                        ->first();

        $involedUsers = array_unique(
                array(
                    $translation->translator,
                    $translation->reviewer1,
                    $translation->reviewer2,
                    $translation->recommender
                )
            );

        $userList = DB::table('user')
                    ->select('id', 'name', 'avatar')
                    ->whereIn('id', $involedUsers)
                    ->get();

        $user = array(null);
        foreach ($userList as $u) {
            $user[$u->id] = $u;
        }

        $translation->translator  = $user[$translation->translator];
        $translation->reviewer1   = $user[$translation->reviewer1];
        $translation->reviewer2   = $user[$translation->reviewer2];
        $translation->recommender = $user[$translation->recommender];
        $translation->timeline    = LogController::readTimeline($id);

        echo json_encode($translation);
    }

    /**
     * 更新文章信息（管理员）
     * @param  Request  $request 文章信息
     * @param  int      $id      文章 ID
     * @return void
     */
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            header('HTTP/1.1 400 Bad request');
            echo json_encode(['message' => '参数错误！']);
            return;
        }

        $this->isNotNull(array(
                '翻译积分'  => $request->input('tscore'),
                '校对积分'  => $request->input('rscore'),
                '翻译时长'  => $request->input('tduration'),
                '校对时长'  => $request->input('rduration'),
                '文章字数'  => $request->input('word')
        ));

        $data = array(
                'tscore'        => $request->input('tscore'),
                'rscore'        => $request->input('rscore'),
                'tduration'     => $request->input('tduration'),
                'rduration'     => $request->input('rduration'),
                'word'          => $request->input('word'),
                'poster'        => $request->input('poster'),
                'description'   => $request->input('description')
            );

        $result = DB::table('translation')
                    ->where('id', $id)
                    ->update($data);

        if ($result == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '更新失败！']);
            return;
        }
    }

    /**
     * 发布文章
     * @param  Request  $request 文章信息
     * @param  int      $id      文章 ID
     * @return void
     */
    public function post(Request $request, $id)
    {
        if (!is_numeric($id)) {
            header('HTTP/1.1 400 Bad request');
            echo json_encode(['message' => '参数错误！']);
            return;
        }
        
        $this->isNotNull(array(
                '用户 ID'   => $request->input('uid'),  
                '文章链接'  => $request->input('link'),
                '文章简介'  => $request->input('description')
        ));

        $data = array(
                'link'          => $request->input('link'),
                'description'   => $request->input('description'),
                'status'        => 4,
                'udate'     => date("Y-m-d H:i:s")
            );

        $result = DB::table('translation')
                    ->where('id', $id)
                    ->update($data);

        if ($result == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '更新失败！']);
            return;
        }

        $tInfo = DB::table('translation')
                    ->select('translator', 'reviewer1', 'reviewer2', 'tscore', 'rscore')
                    ->where('id', $id)
                    ->first();

        UserController::incrementTranslate($tInfo->translator);
        UserController::incrementReview($tInfo->reviewer1);
        UserController::incrementReview($tInfo->reviewer2);
        UserController::incrementScore($tInfo->translator, $tInfo->tscore);
        UserController::incrementScore($tInfo->reviewer1, $tInfo->rscore);
        UserController::incrementScore($tInfo->reviewer2, $tInfo->rscore);

        LogController::writeTimeline(array(
            'tid'       => $id,
            'uid'       => $request->input('uid'),
            'operation' => "分享到掘金",
            'cdate'     => date("Y-m-d H:i:s")
        ));
    }

    /**
     * 请求翻译 （修改文章为待认领状态）
     * @param  Request $request PR 触发的 WebHooks 请求
     * @return boolean
     */
    public function requestTranslate(Request $request)
    {
        $file = $this->getPRFile($request->input('payload'));
        $data = array(
            'status'    => 0,
            'udate'     => date('Y-m-d H:i:s')
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
            '译者 ID' => $request->input('uid')
        ));

        $data = array(
            'translator' => $request->input('uid'),
            'status'     => 1,
            'udate'      => date('Y-m-d H:i:s')
        );

        $result = DB::table('translation')
            ->where('id', $request->input('id'))
            ->update($data);

        if ($result == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '认领失败！']);
            return;
        }

        LogController::writeTimeline(array(
            'tid'       => $request->input('id'),
            'uid'       => $request->input('uid'),
            'operation' => "认领翻译",
            'cdate'     => date("Y-m-d H:i:s")
        ));
    }

    /**
     * 请求校对 （修改文章为待校对状态）
     * @param  Request $request PR 触发的 WebHooks 请求
     * @return boolean
     */
    public function requestReview(Request $request)
    {
        $file = $this->getPRFile($request->input('payload'));
        $data = array(
            'pr'        => $this->getPR($request->input('payload'))->id,
            'status'    => 2,
            'udate'     => date('Y-m-d H:i:s')
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
            '文章 ID'     => $request->input('id'),
            '校对者 ID'   => $request->input('uid')
        ));

        $reviewer1 = DB::table('translation')
                    ->where('id', $request->input('id'))
                    ->value('reviewer1');

        $data =  array(
                'reviewer1' => $reviewer1 ? $reviewer1 : $request->input('uid'),
                'reviewer2' => $reviewer1 ? $request->input('uid') : '',
                'status'    => $reviewer1 ? 3 : 2,
                'udate'     => date('Y-m-d H:i:s')
            );

        $result = DB::table('translation')
                ->where('id', $request->input('id'))
                ->update($data);

        if ($result == false) {
            header('HTTP/1.1 503 Service not available!');
            echo json_encode(['message' => '认领失败！']);
            return;
        }

        LogController::writeTimeline(array(
            'tid'       => $request->input('id'),
            'uid'       => $request->input('uid'),
            'operation' => "认领校对",
            'cdate'     => date("Y-m-d H:i:s")
        ));
    }

    /**
     * 翻译完成 
     * @param  Request $request PR 触发的 WebHooks 请求
     * @return boolean
     */
    public function requestPost(Request $request)
    {
        $file = $this->getPRFile($request->input('payload'));
        $data = array(
            'title'     => $this->getPR($request->input('payload'))->title,
            'udate'     => date('Y-m-d H:i:s')
        );

        $result = $this->updateByFile($file, $data);

        if ($result) {
            $tInfo = DB::table('translation')
                        ->select('id', 'translator')
                        ->where('file', $file)
                        ->first();

            LogController::writeTimeline(array(
                'tid'       => $tInfo->id,
                'uid'       => $tInfo,
                'operation' => "翻译完成",
                'cdate'     => date("Y-m-d H:i:s")
            ));
        }

        return $result;
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
     * @param  object   $payload PR 触发的 WebHooks 中的请求内容
     * @return string   修改的文件名 
     */
    public function getPRFile($payload)
    {
        $diff_url = $payload->pull_request->diff_url;
        preg_match("/^diff --git a\/(.*?) b\/(.*?)\n/", $this->sendRequest($diff_url, 'GET'), $file);

        return $file[1];
    }

    /**
     * 获取 WebHooks 请求中 PR 的信息
     * @param  Object   $payload    PR 触发的 WebHooks 请求内容
     * @return object   PR 信息
     */
    public function getPR($payload)
    {
        return $payload->pull_request;
    }
}
