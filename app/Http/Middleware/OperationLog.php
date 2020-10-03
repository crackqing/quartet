<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Server\Operation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class OperationLog
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

        $routeName = Route::currentRouteName();
            #data,表示是databels插件，直接跳过检测 skip 表示不做记录
        if (mb_strstr($routeName,'data') === FALSE 
                && mb_strstr($routeName,'operation') === FALSE 
                && mb_strstr($routeName,'skip') === FALSE
                && mb_strstr($routeName,'search') === FALSE ) {
           $this->operation($request);
        }

        return $next($request);
    }
    /**
     * [operation get,POST都需要记录后台对应的管理员行为
     * 
     * 1.普通url的访问日志，开启nginx 的 access_log 就有；
     * 2.数据库操作日志，监听SQL就行。如果你想按照自己的意愿实现可读性更高的日志的话，那就自己写，具体根据你想要的效果去实现，
     *  2.1 ELK了解一下...实现日志的聚合查询]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function operation($request)
    {
        $id = Auth::guard('api')->id();

        $data = [
            'manager_id'    => $id,
            'path'  => $request->path(),
            'method'    => $request->method(),
            'ip'    => $request->ip(),
            'input' => json_encode($request->except(['_token']),JSON_UNESCAPED_UNICODE)  
        ];
        Operation::create($data);
    }

}
