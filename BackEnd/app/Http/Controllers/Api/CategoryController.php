<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * 获取所有文章类别
     * @return json_encode(Array)
     */
    public function index()
    {
        //
        $category = DB::table('category')
                    ->select('id', 'category', 'description')
                    ->get();

        echo json_encode($category);
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
     * 添加文章类别
     * @param  string   $category       分类名称
     * @param  string   $description    分类描述
     * @return json_encode(Array)
     */
    public function store(Request $request)
    {
        //
        $this->isNotNull(array(
                $request->input('category')
            ));
        $this->isUnique('category', array(
                'category' => $request->input('category')
            ));
        $data = array(
                'category'      => $request->input('category'),
                'description'   => $request->input('description'),
                'udate'         => date('Y-m-d H:i:s'),
                'cdate'         => date('Y-m-d H:i:s')
            );

        $lastId = DB::table('category')
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
        $category = DB::table('category')
                    ->where('id', $id)
                    ->select('id', 'category', 'description')
                    ->first();

        if ( $category == false ) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['message' => '参数错误！']);
            return;
        }

        echo json_encode($category);
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
     * 更新文章类别
     * @param  string   $category       分类名称
     * @param  string   $description    分类描述
     * @param  int      $id             分类 ID
     * @return json_encode(Array)
     */
    public function update(Request $request, $id)
    {
        //
        $this->isNotNull(array(
                $request->input('category'),
                $request->input('description')
            ));
        $this->isUpdateConflict('category', $id, array(
                'category'      => $request->input('category'),
                'description'   => $request->input('description')
            ));
        $data = array(
                'category'      => $request->input('category'),
                'description'   => $request->input('description'),
                'udate'         => date('Y-m-d H:i:s')
            );

        $result = DB::table('category')
                    ->where('id', $id)
                    ->update($data);

        if ( $result == false ) {
            header("HTTP/1.1 503 Service Unavailable");
            return;
        }

        $this->show($id);
    }

    /**
     * 删除文章类别（不可恢复）
     * @param  int      $id             分类 ID
     * @return json_encode(Array)
     */
    public function destroy($id)
    {
        //
        $result = DB::table('category')
                    ->where('id', $id)
                    ->delete();

        if ( $result == false ) {
            header("HTTP/1.1 503 Service Unavailable");
            return;
        }
    }
}
