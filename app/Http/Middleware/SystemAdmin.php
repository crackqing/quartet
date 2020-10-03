<?php

namespace App\Http\Middleware;

use Closure;

class SystemAdmin
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
        if ($request->session()->has('system_admin')) {
            return $next($request);
        }
        return redirect()->guest('adminTeQ8E5D8/login');

        return $next($request);
    }
}
