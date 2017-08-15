<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    //
    public function index()
    {
        $this->recommend();
    }

    public function recommend()
    {
        $this->week('推荐成功');
    }

    public function translate()
    {

    }

    public function review()
    {

    }

    public function week($field)
    {
        $time = strtotime("-7 days");
        $sql = "SELECT user.id, user.name, user.avatar, COUNT(timeline.uid) as {$field} FROM timeline, user WHERE timeline.uid = user.id AND timeline.cdate > {$time} AND timeline.operation = '{$field}' GROUP BY user.id, user.name, user.avatar ORDER BY {$field} DESC LIMIT 20";
    	$result = DB::select($sql);

        dd($result);
    }

    public function month()
    {

    }

    public function year()
    {

    }
}
