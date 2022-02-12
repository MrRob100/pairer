<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PairsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($_SERVER['SERVER_ADDR'] === '127.0.0.1') {
            return $next($request);
        }

        if ($request->user()) {
            return $next($request);
        }

        return redirect('/login');
    }
}
