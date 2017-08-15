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
        $result = array();
        $result['recommend'] = $this->recommend();
        $result['translate'] = $this->translate();
        $result['review'] = $this->review();

        echo json_encode($result);
    }

    public function recommend()
    {
        $result = array();

        $result['week'] = $this->week('推荐成功');
        $result['month'] = $this->month('推荐成功');
        $result['year'] = $this->year('推荐成功');

        return $result;
    }

    public function translate()
    {
        $result = array();

        $result['week'] = $this->week('认领翻译');
        $result['month'] = $this->month('认领翻译');
        $result['year'] = $this->year('认领翻译');

        return $result;
    }

    public function review()
    {
        $result = array();

        $result['week'] = $this->week('认领校对');
        $result['month'] = $this->month('认领校对');
        $result['year'] = $this->year('认领校对');

        return $result;
    }

    public function week($field)
    {
        $time = strtotime("-1 week");
        $sql = "SELECT user.id, user.name, user.avatar, COUNT(timeline.uid) AS num FROM timeline, user WHERE timeline.uid = user.id AND timeline.cdate > {$time} AND timeline.operation = '{$field}' GROUP BY user.id, user.name, user.avatar ORDER BY num DESC LIMIT 20";

    	return DB::select($sql);
    }

    public function month($field)
    {
        $time = strtotime("-1 month");
        $sql = "SELECT user.id, user.name, user.avatar, COUNT(timeline.uid) AS num FROM timeline, user WHERE timeline.uid = user.id AND timeline.cdate > {$time} AND timeline.operation = '{$field}' GROUP BY user.id, user.name, user.avatar ORDER BY num DESC LIMIT 20";
        
        return DB::select($sql);
    }

    public function year($field)
    {
        $time = strtotime("-1 year");
        $sql = "SELECT user.id, user.name, user.avatar, COUNT(timeline.uid) AS num FROM timeline, user WHERE timeline.uid = user.id AND timeline.cdate > {$time} AND timeline.operation = '{$field}' GROUP BY user.id, user.name, user.avatar ORDER BY num DESC LIMIT 20";
        
        return DB::select($sql);
    }

    public function overview()
    {
        $result = array();
        $result['translators'] = $this->translator();
        $result['words'] = $this->word();
        $result['articles'] = $this->article();

        echo json_encode($result);
    }

    public function translator()
    {
        return DB::table('user')
                ->where('translator', '1')
                ->count('id');
    }

    public function word()
    {
        return DB::table('translation')
                ->sum('word');
    }

    public function article()
    {
        return DB::table('translation')
                ->count('id');
    }
}
