<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function oAuth()
    {
        $url = "https://github.com/login/oauth/authorize";
        $url .= "?client_id=".$this->client_id;
        $url .= "&state=".$this->randomString(32);

        return redirect($url);
    }

    public function logout(Request $request)
    {

        $token = $request->header('authorization');
        $result = DB::table('userToken')->where('token', $token)->delete();

        if ($result) {
            header('HTTP/1.1 200 ok');
        } else {
            header('HTTP/1.1 501 Not Implemented');
        }
    }
}
