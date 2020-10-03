<?php

namespace App\Http\Middleware;

use Closure;
use Config;
class LimitIpAccess
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
        #限制访问IP  与 本地不限制处理 调试处理
        // if (Config::get('app.env')  != 'local') {
        //     $ip  = $request->ip();
        //     $serverIp = [
        //         #DF 
        //         '58.82.248.229',
        //         '58.82.212.190',
        //         #JG
        //         '154.222.142.113',
        //         '154.222.142.111',
        //         #TT
        //         '58.82.202.89',
        //         '58.82.202.92',
        //         #hreo
        //         '45.195.144.78',
        //         '45.195.144.231'
        //     ];
        // } 
        // $ip  = $request->ip();
        // $serverIp = [
        //     #DF 
        //     '58.82.248.229',
        //     '58.82.212.190',
        //     #JG
        //     '154.222.142.113',
        //     '154.222.142.111',
        //     #TT
        //     '58.82.202.89',
        //     '58.82.202.92',
        //     #hreo
        //     '45.195.144.78',
        //     '45.195.144.231'
        // ];


        return $next($request);
    }
}
