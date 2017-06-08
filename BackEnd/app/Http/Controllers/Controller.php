<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * GitHub 注册应用 client_id
     * @var string
     */
    protected $client_id;

    /**
     * GitHub 注册应用 client_secret
     * @var string
     */
    protected $client_secret;

    /**
     * 获取数据起始位置
     * @var integer
     */
    protected $start = 0;

    /**
     * 每次获取数据量
     * @var integer
     */
    protected $offset = 10;

    public function __construct(Request $request)
    {
    	$this->client_id = config('app.github_client_id');
    	$this->client_secret = config('app.github_client_secret');

        if ( $request->has('per_page') ) {
            $this->offset = $request->input('per_page');
        }

        if ( $request->has('page') ) {
            $this->start = ($request->input('page') - 1) * $this->offset;
        }
    }

    /**
     * 获取指定长度的随机字符串
     * @param  int 		$length 	随机字符串长度
     * @return string   			指定长度的随机字符串
     */
    public function randomString($length)
    {
        $range = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randomString = '';
        for($i = 0; $i < $length; $i++){
            $index = rand(0,strlen($range));
            $randomString .=substr($range, $index-1, 1);
        }

        return $randomString;
    }

    /**
     * 发送 HTTP 请求
     * @param  string 	$url    请求地址
     * @param  string 	$method 请求方法，'GET' 或者 'POST'
     * @param  array  	$params 请求参数
     * @return mixd         	HTTP 请求返回值
     */
    public function sendRequest($url, $method, $params = array()) 
    {
        $options = array(
                CURLOPT_USERAGENT       =>  'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36',
                CURLOPT_HTTPHEADER      =>  array(
                        'Content-type'      =>  'application/x-www-form-urlencoded'                    ),
                // CURLOPT_HEADER          =>  true,
                CURLOPT_RETURNTRANSFER  =>  true
            );
        
        $query = http_build_query($params);

        $options[CURLOPT_URL] = $url.($method == 'GET' ? '?'.$query : '');

        if ($method == 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $query;
        }

        $sr = curl_init();
        curl_setopt_array($sr, $options);
        $ret = curl_exec($sr);
        curl_close($sr);

        return $ret;
    }

    /**
     * 生成随机的唯一的 token
     * @param  string   $flag   用户标志（唯一）
     * @return string   token
     */
    public function generateToken($flag)
    {
        return md5($this->randomString(32).time().$flag);
    }

    /**
     * 检查参数是否为空
     * @param  array  $params 被检查参数列表
     * @return void 
     */
    public function isNotNull($params = array())
    {
        foreach ($params as $v) {
            if (empty($v)) {
                header("HTTP/1.1 400 Bad Request");
                die(json_encode(['message' => '参数不能为空！']));
            }
        }
    }

    /**
     * 检查参数是否唯一
     * @param  string  $table  目标数据表
     * @param  array   $fields 目标参数
     * @return viod 
     */
    public function isUnique($table, $fields = array())
    {
        $table = env('DB_PREFIX').$table;
        $where = '';
        foreach ($fields as $k => $v) {
            $where .= $k."='".$v."' OR ";
        }

        $record = DB::select("SELECT * FROM ".$table." WHERE ".rtrim($where, 'OR '));

        if ($record) {
            foreach ($fields as $k => $v) {
                if ($v == $record[0]->$k) {
                    header("HTTP/1.1 409 Conflict");
                    die(json_encode(['message' => $k.' 参数重复！']));
                }
            }
        }
    }
    /**
     * 检查更新参数是否冲突
     * @param  string  $table  目标数据表
     * @param  int     $id     更新记录的 ID
     * @param  array   $fields 目标参数
     * @return viod 
     */
    public function isUpdateConflict($table, $id, $fields = array())
    {
        $table = env('DB_PREFIX').$table;
        $where = '';
        foreach ($fields as $k => $v) {
            $where .= $k."='".$v."' OR ";
        }

        $record = DB::select("SELECT * FROM ".$table." WHERE id!=".$id." AND (".rtrim($where, 'OR ').")");

        if ($record) {
            foreach ($fields as $k => $v) {
                if ($v == $record[0]->$k) {
                    header("HTTP/1.1 409 Conflict");
                    die(json_encode(['message' => $k.' 参数重复！']));
                }
            }
        }
    }

    /**
     * 检查邮箱格式
     * @param  string   $email  邮箱
     * @return json_encode(Array)
     */
    protected function checkEmail( $email )
    {
        if ( false == preg_match('/(.*?)@(.*?)\.(.*?)/', $email) ) {
            header("HTTP/1.1 400 Bad Request");
            die(json_encode(['message' => '邮箱格式错误！']));
        }
    }
}
