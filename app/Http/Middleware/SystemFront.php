<?php

namespace App\Http\Middleware;

use Closure;

class SystemFront
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
        if ($request->session()->has('system_front_id')) {
            return $next($request);
        }
        return redirect()->guest('front/login');

        return $next($request);
    }
}
