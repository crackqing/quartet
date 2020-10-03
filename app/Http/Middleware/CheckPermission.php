<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Service\ApiResponse;

class CheckPermission
{
    use ApiResponse;
    /**
     * Handle 权限检查，涉及所有的后台操作.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeName = Route::currentRouteName();
        #data,表示是databels插件 . 直接跳过检测 skip 表示不验证，直接跳过。
        if (mb_strstr($routeName, 'skip') === false) {
            if ($this->check($routeName)) {
                 return $next($request);
            } else {
                return $this->nativeRespond(403, ['message'=> '如需权限,请联系管理员进行操作'], 'error');
            }
        }
        return $next($request);

    }
    /**
     * [check 权限判断，不存在则403]
     * @param  [type] $request    [description]
     * @param  [type] $permission [description]
     * @return [type]             [description]
     */
    public function check($permission)
    {
        #单个栏目存在多权限的可以，需要检测,是否存在与show ,edit 只检测动做

        // \Log::info('permission_role_check',['permission' => $permission]);

        #编辑与查看单个操作就不检测了，防止选项太多造成理解上的问题
       if (mb_stristr($permission,'show') !== false 
            || mb_stristr($permission,'edit') !== false
            || mb_stristr($permission,'front') !== false) {
            return true;
       } 

        $user = Auth::guard('api')->user();
        #ID为1的直接跳过,不检测操作
        if ($user->id == 1) {
            return true;
        }
        if (!$user->can($permission)) {
            return false;
        }
        return true;
    }
}
