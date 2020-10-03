<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;

use App\Http\Requests\Api\LoginRequest;
use App\Service\Api\LoginService;

/** 登录控制器,用于API登录,令牌更新以及退出操作.
 * Class LoginController
 * @package App\Http\Controllers\Api
 */
class LoginController extends BaseController
{
    protected $loginService;

    /** Service分层
     * LoginController constructor.
     *
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }
    /** passport auth api
     * @param LoginRequest $request
     *
     * @return mixed
     */
    public function login(LoginRequest $request)
    {
        return $this->loginService->login($request);
    }
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function update(Request $request)
    {
        return $this->loginService->update($request);
    }
    /**
     * @return mixed
     */
    public function destroy()
    {
        return $this->loginService->destroy();
    }


    /** thridPlatform : weixin qq
     * @param $type
     * @param Request $request
     *
     * @return mixed
     */
    public function socialStore($type,Request $request)
    {

        return $this->loginService->socialStoreService($type,$request);
    }


}
