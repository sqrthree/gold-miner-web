<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        "translations/pr"
    ];

    public function handle($request, \Closure $next)
    {
    	if ($request->getClientIP() == "127.0.0.1") {
            return $next($request);
    	}

    	parent::handle();
    }
}
