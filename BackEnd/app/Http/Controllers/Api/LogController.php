<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    //
    public static function writeTimeline($data)
    {
        return DB::table('timeline')->insert($data);
    }

    public static function readTimeline($id)
    {
    	return DB::table('timeline')
    			->leftJoin('user', 'timeline.uid', '=', 'user.id')
    			->select('timeline.operation', 'timeline.cdate', 'user.id as userId', 'user.name', 'user.avatar')
    			->where('timeline.tid', $id)
    			->get();
    }
}