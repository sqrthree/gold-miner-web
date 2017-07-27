<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    protected $notifications = array();
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $applicants = DB::table('applicant')
                        ->leftjoin('user', 'user.id', '=', 'applicant.uid')
                        ->where('applicant.status', 0)
                        ->select('applicant.id', 'applicant.name', 'applicant.cdate', 'user.name as userName', 'user.avatar as avatar')
                        ->orderBy('applicant.id', 'ASC')
                        ->get();

        $recommends = DB::table('recommend')
                        ->join('user', 'recommend.recommender', '=', 'user.id')
                        ->where('recommend.status', 0)
                        ->select('recommend.id', 'recommend.title', 'recommend.cdate', 'user.name', 'user.avatar')
                        ->orderBy('recommend.id', 'ASC')
                        ->get();

        $this->notifications['applicants'] = $applicants;
        $this->notifications['recommends'] = $recommends;
        $this->notifications['total'] = count($applicants) + count($recommends);

        echo json_encode($this->notifications);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
