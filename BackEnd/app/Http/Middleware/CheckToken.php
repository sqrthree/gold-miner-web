<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod("get") || $request->getClientIp() == "127.0.0.1") {
            return $next($request);
        }

        if ($userToken = $request->header('authorization')) {
            $token = DB::table('userToken')
                    ->where('token', $userToken)
                    ->first();

            if ($token == null) {
                header("HTTP/1.1 401 Unauthorized");
                echo json_encode(['message' => 'Token 错误！']);
                die;
            }

            if (strtotime($token->expiry) < time()) {
                header("HTTP/1.1 401 Unauthorized");
                echo json_encode(['message' => 'Token 已过期！']);
                die;
            }
        } else {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['message' => 'Token 丢失！']);
            die;
        }

        if (strtotime($token->expiry) - time() < 86400) {
            $this->updateToken($userToken);
        }

        return $next($request);
    }

    public function updateToken($token)
    {
        $data = array(
                'expiry'    => date('Y-m-d H:i:s', strtotime('+1 week')),
                'udate'     => date('Y-m-d H:i:s')
            );

        $res = DB::table('userToken')
                ->where('token', $token)
                ->update($data);
    }
}
