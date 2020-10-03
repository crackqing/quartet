<?php

namespace App\Http\Middleware;

use Closure;

class SystemProfit
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
        if ($request->session()->has('system_profit_id')) {
            return $next($request);
        }
        return redirect()->guest('profit/login');

        return $next($request);
    }
}
