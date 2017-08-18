<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * Statistics job
 *
 * Class StatisticController
 * @package App\Http\Controllers\Api
 */
class StatisticController extends Controller
{
    /**
     * 获取排行榜数据
     */
    public function index()
    {
        $result = array();
        $result['recommend'] = $this->recommend();
        $result['translate'] = $this->translate();
        $result['review'] = $this->review();

        echo json_encode($result);
    }

    /**
     * 获取推荐文章排行榜
     * @return array
     */
    public function recommend()
    {
        $result = array();

        $result['week'] = $this->week('推荐成功');
        $result['month'] = $this->month('推荐成功');
        $result['year'] = $this->year('推荐成功');

        return $result;
    }

    /**
     * 获取翻译文章排行榜
     * @return array
     */
    public function translate()
    {
        $result = array();

        $result['week'] = $this->week('认领翻译');
        $result['month'] = $this->month('认领翻译');
        $result['year'] = $this->year('认领翻译');

        return $result;
    }

    /**
     * 获取校对文章排行榜
     * @return array
     */
    public function review()
    {
        $result = array();

        $result['week'] = $this->week('认领校对');
        $result['month'] = $this->month('认领校对');
        $result['year'] = $this->year('认领校对');

        return $result;
    }

    /**
     * 获取指定类别的上周排行
     * @param $field
     * @return mixed
     */
    public function week($field)
    {
    	return DB::table('timeline')
                ->join('user', 'timeline.uid', '=', 'user.id')
                ->select(DB::raw("user.id, user.name, user.avatar, COUNT(timeline.uid) AS num"))
                ->where("timeline.cdate", ">", strtotime("-1 week"))
                ->where("timeline.operation", $field)
                ->groupBy("user.id", "user.name", "user.avatar")
                ->orderBy("num", "DESC")
                ->limit(20)
                ->get();
    }

    /**
     * 获取指定类别的上月排行
     * @param $field
     * @return mixed
     */
    public function month($field)
    {
        return DB::table('timeline')
            ->join('user', 'timeline.uid', '=', 'user.id')
            ->select(DB::raw("user.id, user.name, user.avatar, COUNT(timeline.uid) AS num"))
            ->where("timeline.cdate", ">", strtotime("-1 month"))
            ->where("timeline.operation", $field)
            ->groupBy("user.id", "user.name", "user.avatar")
            ->orderBy("num", "DESC")
            ->limit(20)
            ->get();
    }

    /**
     * 获取指定类别的上年排行
     * @param $field
     * @return mixed
     */
    public function year($field)
    {
        return DB::table('timeline')
            ->join('user', 'timeline.uid', '=', 'user.id')
            ->select(DB::raw("user.id, user.name, user.avatar, COUNT(timeline.uid) AS num"))
            ->where("timeline.cdate", ">", strtotime("-1 year"))
            ->where("timeline.operation", $field)
            ->groupBy("user.id", "user.name", "user.avatar")
            ->orderBy("num", "DESC")
            ->limit(20)
            ->get();
    }

    /**
     * 获取全站统计数字概览
     */
    public function overview()
    {
        $result = array();
        $result['translators'] = $this->translator();
        $result['words'] = $this->word();
        $result['articles'] = $this->article();

        echo json_encode($result);
    }

    /**
     * 获取全站译者数目
     * @return mixed
     */
    public function translator()
    {
        return DB::table('user')
                ->where('translator', '1')
                ->count('id');
    }

    /**
     * 获取全站词汇数目
     * @return mixed
     */
    public function word()
    {
        return DB::table('translation')
                ->sum('word');
    }

    /**
     * 获取全站文章数目
     * @return mixed
     */
    public function article()
    {
        return DB::table('translation')
                ->count('id');
    }
}
