<?php
namespace App\Service\Api;
use App\Repositories\Eloquent\UserEloquent;
use App\Repositories\Eloquent\ActiveEloquent;
use App\Models\Enum\UserEnum;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use GuzzleHttp\Client;
use Config,Log;

/**
 * Class LoginService
 * @package App\Service\Api
 */
class LoginService  extends BaseService
{
    use AuthenticatesUsers;

    protected $userEloquent;

    protected $activeEloquent;

    /** M使用R分层 github https://github.com/andersao/l5-repository
     * LoginService constructor.
     *
     * @param UserEloquent $userEloquent
     * @param ActiveEloquent $activeEloquent
     */
    public function __construct(UserEloquent $userEloquent,ActiveEloquent $activeEloquent)
    {
        $this->userEloquent = $userEloquent;
        $this->activeEloquent = $activeEloquent;
    }

    /** LoginController service
     * @param Request $request
     *
     * @return mixed
     */
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $user = $this->userEloquent->findWhere([
            'email' => $email
        ]);
        if(empty($user) || $user[0]['status'] == UserEnum::FREEZE || $user[0]['manager']  != UserEnum::MANAGER){
            return  $this->nativeRespond('10',[],'帐号异常!');
        }
        if ($this->guard('api')->attempt($this->credentials($request), $request->has('remember'))) {
            $this->activeEloquent->activeLog($request,$user[0]['id'],UserEnum::NORMAL);
            $http = new Client();
            $url = Config::get('services.oauth_api');
            $response = $http->post($url,[
                'form_params'   => [
                    'grant_type'    => 'password',
                    'client_id' => '2',
                    'client_secret' => '7pWbYdhhfJKLdREFIKFqCOohsDqxSBRURUyvsqc0',
                    'username'  => $email,
                    'password'  => $password,
                    'scope' => '*'
                ]
            ]);
            return json_decode((string)$response->getBody(), true);
        }
        $this->activeEloquent->activeLog($request,$user[0]['id'],UserEnum::LOGINERROR);
        return  $this->nativeRespond('10',[],'登录密码错误或帐号错误!');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function update(Request $request)
    {
        $http = new Client();
        $url = Config::get('services.oauth_api');
        $response = $http->post($url,[
            'form_params'   => [
                'grant_type'    => 'refresh_token',
                'client_id' => '2',
                'client_secret' => '7pWbYdhhfJKLdREFIKFqCOohsDqxSBRURUyvsqc0',
                'refresh_token'  => (string) $request->refresh_token,
                'scope' => '*'
            ]
        ]);
        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @return mixed
     */
    public function destroy()
    {
        if (\Auth::guard('api')->check()) {
            \Auth::guard('api')->user()->token()->delete();
        }
        return  $this->message('清空成功!');
    }

    /** passport 里面自带完整的auth2.0登录方式
     * @param Request $request
     *
     * @return bool|mixed
     */
    public function client(Request $request)
    {
        $client_id = $request->client_id ?? '';
        $client_secret = $request->client_secret ?? '';

        if (!$client_id && !$client_secret) {
            return false;
        }
        $http = new Client();
        $url = Config::get('services.oauth_api');
        $response = $http->post($url,[
            'form_params'   => [
                'grant_type'    => 'client_credentials',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'scope' => '*'
            ]
        ]);
        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param $type
     * @param $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function socialStoreService($type,$request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->failed('errorThridType');
        }
        if (empty($request->code) && $type =='weixin'){
            $appid = Config::get('services.weixin.client_id');
            $redirect = Config::get('services.weixin.redirect');
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            return  redirect($url);
        }
        $driver = \Socialite::driver($type);
        Log::info('socialStoreService==>',['type'=>$type,'request'=>$request->all(),'driver'=>$driver]);
        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;
                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }
            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            return $this->failed('参数错误，未获取用户信息');
        }
        switch ($type) {
            case 'weixin':
//                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;
                    //测试用remark当作openid来测试
                $user = $this->userEloquent->findWhere([
                    'remark' => $oauthUser->getId()
                ]);
//                if ($unionid) {
//                    $user = User::where('weixin_unionid', $unionid)->first();
//                } else {
//                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
//                }
                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = $this->userEloquent->create([
                        'name' => $oauthUser->getNickname(),
                        'email' => $oauthUser->getAvatar(),
                        'remark' => $oauthUser->getId(),
                        'password'  => 'test',
//                        'weixin_unionid' => $unionid,
                    ]);
                }
                Log::info('socialWEIXIN==>',['user'=>$user,'openid'=>$oauthUser->getId(),'email'=> $oauthUser->getAvatar(),'name'=>$oauthUser->getNickname()  ]);
                break;
        }
        return $this->success($user);
    }
}