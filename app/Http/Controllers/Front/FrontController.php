<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Hash,Config,Cache;
use App\Traits\CheckLogin;

class FrontController extends Controller
{
    use CheckLogin;

    public function login(Request $request)
    {
        if ($request->session()->has('system_front_id')) {
            return  redirect('front/dashboard');
        }
        return view('Front.login.login');
    }
    /**
     * 盈利分成代理后台 function
     *
     * @return void
     */
    public function profit(Request $request)
    {
        if ($request->session()->has('system_front_id')) {
            return  redirect('profit/dashboard');
        }
        return view('Front.login.profix');
    }



    public function loginDo(Request $request)
    {
		$LoginLimit = 'LoginLimit:'.$request->getClientIp();
		$limit = Cache::get($LoginLimit);
		if ($limit >= 6) {
			return response()->json(Config::get('resjson.12'));
		} 
        $user =  User::where('email', $request->phone)->first();

        if (empty($user)) {
            return response()->json(Config::get('resjson.10'));
        }
        if ($user->status == 1) {
            return response()->json(Config::get('resjson.101'));
        }
        #手动验证哈希,存放session .不使用系统的auth验证. 避免冲突
        if (Hash::check($request->password,$user->password)) {

            $request->session()->put('system_front_id', $user->id);
            $request->session()->put('system_profit_id', $user->id);
            $request->session()->put('system_front_user',$user);

            $this->agentAccessLogin($request,true);

            return response()->json(Config::get('resjson.200'));
        } 
        #检测错误次数,防止重复登录与撞库== 与记录登录成功的情况与失败
        $this->ipAccessRestrictions($request->getClientIp());
        $this->agentAccessLogin($request,false);

        return response()->json(Config::get('resjson.10'));
    }

    /**
     * 退出登录操作 function
     *
     * @return void
     */
    public function exitLogin(Request $request)
    {
        session()->forget('system_front_id');
        session()->forget('system_profit_id');
        session()->forget('system_front_user');

        // if (mb_stripos($request->path(),'profit') !== false ) {
    	// 	return redirect('profit/login');
        // }
        // return redirect('front/login');
        
		return redirect('/');

    }

}
