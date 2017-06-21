<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RecommendController extends Controller
{
    /**
     * 获取所有推荐文章记录（分页）
     * @return json_encode(Object) 全部推荐文章（分页）
     */
    public function index()
    {
        //
        $recommends = DB::table('recommend')
                        ->join('user', 'recommend.recommender', '=', 'user.id')
                        ->select('recommend.*', 'user.name as recommenderName')
                        ->orderBy('status', 'asc')
                        ->skip($this->start)
                        ->take($this->offset)
                        ->get();

        echo json_encode($recommends);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 添加推荐文章记录
     * @param  int      $category       文章分类（对应 category 表）
     * @param  string   $title          文章标题
     * @param  string   $url            文章来源地址
     * @param  int      $recommender    推荐者（对应 user 表）
     * @param  string   $description    推荐理由
     * @return json_encode(Array)
     */
    public function store(Request $request)
    {
        //
        $this->isNotNull(array(
                'category'      => $request->input('category'),
                'title'         => $request->input('title'),
                'url'           => $request->input('url'),
                'recommender'   => $request->input('recommender'),
                'description'   => $request->input('description')
            ));
        $this->isUnique('recommend', array(
                'url' => $request->input('url')
            ));
        $data = array(
                'category'      => $request->input('category'),
                'title'         => $request->input('title'),
                'url'           => $request->input('url'),
                'recommender'   => $request->input('recommender'),
                'description'   => $request->input('description'),
                'status'        => 0,
                'udate'         => date('Y-m-d H:i:s'),
                'cdate'         => date('Y-m-d H:i:s')
            );

        $lastId = DB::table('recommend')
                    ->insertGetId($data);

        if ( $lastId == false ) {
            header("HTTP/1.1 503 Service Unavailable");
            return;
        }

        $this->show($lastId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $recommend = DB::table('recommend')
                        ->join('user', 'recommend.recommender', '=', 'user.id')
                        ->select('recommend.*', 'user.name as recommenderName')
                        ->where('recommend.id', $id)
                        ->first();

        if ( $recommend == false ) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['message' => '参数错误！']);
            return;
        }
                        
        echo json_encode($recommend);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 更新推荐文章记录
     * @param  int      $category       文章分类（对应 category 表）
     * @param  string   $title          文章标题
     * @param  string   $url            文章来源地址
     * @param  int      $recommender    推荐者（对应 user 表）
     * @param  string   $description    推荐理由
     * @param  int      $id             文章 ID
     * @return json_encode(Array)
     */
    public function update(Request $request, $id)
    {
        //
        $status = DB::table('recommend')
                    ->where('id', $id)
                    ->value('status');

        if ($status != 0) {
            header("HTTP/1.1 403 forbidden");
            echo json_encode(['message' => '您推荐的文章已被管理员处理，无法操作！']);
            return;
        }

        $this->isNotNull(array(
                'category'      => $request->input('category'),
                'title'         => $request->input('title'),
                'url'           => $request->input('url'),
                'recommender'   => $request->input('recommender'),
                'description'   => $request->input('description')
            ));
        $this->isUpdateConflict('recommend', $id, array(
                'url' => $request->input('url')
            ));

        $data = array(
                'category'      => $request->input('category'),
                'title'         => $request->input('title'),
                'url'           => $request->input('url'),
                'recommender'   => $request->input('recommender'),
                'description'   => $request->input('description'),
                'udate'         => date('Y-m-d H:i:s')
            );
        
        $result = DB::table('recommend')
                        ->where('id', $id)
                        ->update($data);

        if ( $result == false ) {
            header("HTTP/1.1 503 Service Unavailable");
            return;
        }

        $this->show($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * 更新文章处理结果
     * @param  int      $id     文章 ID
     * @param  boolean  $result 推荐结果，true 为通过，false 为未通过
     * @return json_encode(Array)
     */
    public function result($id, $result = true)
    {
        if (!is_numeric($id)) {
            header("HTTP/1.1 400 Bad request");
            echo json_encode(['message' => '参数错误！']);
            return;
        }

        $data = array(
                'status' => 1
            );

        if ($result == false) {
            $data['status'] = 2;
        }

        $result = DB::table('recommend')
                    ->where('id', $id)
                    ->update($data);

        if ( $result == false ) {
            header("HTTP/1.1 503 Service Unavailable");
            return;
        }

        $uid = DB::table('recommend')
                ->where('id', $id)
                ->value('recommender');

        UserController::incrementRecommend($uid);
    }

}
