<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    protected $apiUser;

    protected $apiKey;

    protected $apiUrl;

    public function __construct()
    {
        $this->apiUser  = env('MAILUSER');
        $this->apiKey   = env('MAILKEY');
        $this->apiUrl   = env('MAILURL');
    }

    public function sendMail(Request $request)
    {
        $params = array(
                'apiUser'   => $this->apiUser,
                'apiKey'    => $this->apiKey,
                'from'      => $request->input('from'),
                'to'        => $request->input('to'),
                'subject'   => $request->input('subject'),
                
            );
        $this->sendRequest($this->apiUrl, 'POST', $params);
    }
}
