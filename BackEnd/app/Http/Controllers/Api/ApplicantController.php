<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\MailController as Mail;

class ApplicantController extends Controller
{
    /**
     * 获取全部申请者信息
     * @param int $status 试译记录类别，0 为未处理，1 为成功，2 为失败
     * @return json_encode(Object)  全部申请者（分页）
     */
    public function index(Request $request)
    {
        if (intval($request->input('status')) > 2) {
            header("HTTP/1.1 400 Bad request!");
            echo json_encode(['message' => '参数错误！']);
            return;
        }
        
        $applicants = DB::table('applicant')
                        ->leftjoin('category', 'applicant.major', '=', 'category.id')
                        ->leftjoin('user', 'applicant.uid', '=', 'user.id')
                        ->select('applicant.id', 'applicant.name', 'applicant.email', 'applicant.status', 'applicant.description', 'applicant.articleid as articleId', 'applicant.translation', 'applicant.udate', 'applicant.cdate', 'category.category as major', 'user.avatar as userAvatar', 'user.id as userId')
                        ->where('applicant.status', $request->has('status') ? intval($request->input('status')) : 0)
                        ->orderBy('applicant.id', 'ASC')
                        ->skip($this->start)
                        ->take($this->offset)
                        ->get();

        echo json_encode($applicants);
    }

    /**
     * 添加申请者
     * @param  int      $uid            用户 ID（对应 user 表，如果有的话）
     * @param  string   $name           昵称
     * @param  string   $email          邮箱
     * @param  int      $major          擅长领域
     * @param  string   $description    自我描述
     * @param  string   $translation    试译内容
     * @param  int      $articleId      试译文章 ID
     * @return json_encode(Array)        
     */
    public function store(Request $request)
    {
        $this->isNotNull(array(
                'email'         => $request->input("email"),
                'major'         => $request->input("major"),
                'translation'   => $request->input("translation"),
                'articleId'     => $request->input("articleId")
            ));

        $this->checkEmail($request->input('email'));
        $this->isUnique('applicant', array(
                'email' => $request->input("email")
            ));

        $data = array(
                'uid'           => $request->input('uid'),
                'name'          => $request->input('name'),
                'email'         => $request->input('email'),
                'major'         => $request->input('major'),
                'description'   => $request->input('description'),
                'translation'   => $request->input('translation'),
                'articleid'     => $request->input('articleId'),
                'status'        => 0,
                'udate'         => date('Y-m-d H:i:s'),
                'cdate'         => date('Y-m-d H:i:s')
            );

        $lastId = DB::table('applicant')
                        ->insertGetId($data);

        if($lastId === false){
            header("HTTP/1.1 503 Service Unavailable");
            return;
        }
    }

    /**
     * 获取单个试译者详细信息
     * @param  int  $id     试译者 ID
     * @return json_encode(Object)     
     */
    public function show($id)
    {
        $applicant = DB::table('applicant')
                        ->leftjoin('category', 'applicant.major', '=', 'category.id')
                        ->leftjoin('article', 'applicant.articleid', '=', 'article.id')
                        ->where('applicant.id', $id)
                        ->select('applicant.id', 'applicant.name', 'applicant.email', 'applicant.status', 'applicant.description', 'applicant.translation', 'applicant.udate', 'applicant.cdate', 'category.category as major', 'article.content')
                        ->first();

        if ( $applicant === null ) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['message' => '参数错误！']);
            return;
        }

        echo json_encode($applicant);
    }

    /**
     * 更新申请结果
     * @param  bool     $result     申请结果，true 为成功，false 为失败
     * @param  int      $id         申请记录的 ID
     * @return json_encode(Array)           
     */
    public function update(Request $request, $id, Mail $mail)
    {
        if (!is_numeric($id)) {
            header("HTTP/1.1 400 Bad request");
            echo json_encode(['message' => '参数错误！']);
            return;
        }

        $data = array(
                'comment'   => $request->input('opinion'),
                'status'    => 1,
                'udate'     => date('Y-m-d H:i:s')
            );

        if ( $request->input('result') == true ) {
            $data['invitation'] = $this->generateToken($id);
        } else {
            $data['status'] = 2;
        }

        $result = DB::table('applicant')
                    ->where('id', $id)
                    ->update($data);

        if($result === false){
            header("HTTP/1.1 503 Service Unavailable");
            return;
        }

        $mail->activate($id);
    }

}
