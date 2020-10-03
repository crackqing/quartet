<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\CheckLogin;
use Hash,Config,Cache;

class AdminController extends Controller
{
    use CheckLogin;

    public function login(Request $request)
    {
        if ($request->session()->has('system_admin')) {
            return  redirect('adminTeQ8E5D8/record');
        }
        return view('Admin.login.login');
    }

    public function loginDo(Request $request)
    {
		$LoginLimit = 'LoginLimit:'.$request->getClientIp();
		$limit = Cache::get($LoginLimit);
		if ($limit >= 6) {
			return response()->json(Config::get('resjson.12'));
		} 

        $account = 'admin';
        $password = 'sf159951';

        if ($request->phone !=  $account || $request->password != $password) {

            $this->ipAccessRestrictions($request->getClientIp());

            return response()->json(Config::get('resjson.101'));
        }
        $request->session()->put('system_admin', $account);

        return response()->json(Config::get('resjson.200'));
    }
}
