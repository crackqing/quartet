<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Service\ApiResponse;
use App\Service\QpApi;
use App\Service\sms\chuanglan;

use Auth,Config,Cache;
use App\Service\Paytem\ArrayUtil;
use App\Service\Paytem\XRsa;
use App\Models\Server\ApiConis;
use App\User;

/**
 * Class BaseController 用于控制入口文件,定义常用操作.
 * @package App\Http\Controllers\Api
 */
class BaseController extends Controller
{
    use ApiResponse;

    protected $api;
    protected $sms;

    public function __construct(QpApi $api,chuanglan $sms)
    {
        $this->api = $api;
        $this->sms = $sms;
    }

    public function searchPaginate($model,$number)
    {
        $model = $model->paginate($number);

        return $this;
    }

    public function jsonDecode($request)
    {
        return  json_decode($request->getContent(), true);
    }
    /**
     * [checkParam API传入检测的参数]
     * @param  [type] $param      [description]
     * @param  Array  $checkParam [inviteCode => inviteCode]
     */
    public function checkParam($param,Array $checkParam)
    {
    	if (is_array($param)) {
			foreach ($param as $k => $v) {
				
				if (!array_key_exists($k, $checkParam)) {
				 	return false;
				} 

				if (empty($v)) {
					return false;
				}
				
				 return true;
			}
    	}
    	return false;
    }


    public function userId()
    {
        return Auth::guard('api')->id();
    }

    public function userInfoBase()
    {
        return Auth::guard('api')->user();
    }

    public function userAmount()
    {
        return Auth::guard('api')->user()->amount;
    }
    /**
     * 获取当前传入的ID，当前的信息 function
     */
    public function getUserInfoBase($uid)
    {
        return User::where('id',$uid)->first();
    }

    public function paymentSign(array $data)
    {
        $values =  ArrayUtil::removeKeys($data, ['sign', 'sign_type']);
        $values =  ArrayUtil::paraFilter($values);
        $values =  ArrayUtil::arraySort($values);
        $signStr = ArrayUtil::createLinkstring($values);

        $signStr .= '&key=' . Config::get('services.payment.taizi.app_secret');

        return  strtoupper(md5($signStr)) ;        
    }

    public function paymentRsa($data,$op ='default')
    {
        $XRsa = new XRsa();

        if ($op == 'default') {
            return  $XRsa->publicEncrypt(json_encode($data))  ;
        }
        return  $XRsa->publicDecrypt($data);        
    }

    /**
     * 判断活动是否开启状态 与 当前ID为1管理的余额是否能够继续在赠送 function
     */
    public function bindGiveHandle($user_id)
    {
        if (!Cache::has('gold:giveConfig')) {
            return false;
        }
        $give = Cache::get('gold:giveConfig');

        // Log::info('userDEBUG=========>2222222222222',['give'=>$give]);

        if ($give['give_start'] != 1) {
           return false;
        }
        #默认用的是帐号1的来进行赠送.
        $user = User::where('id', 1)->first();

        // Log::info('userDEBUG=========>',['user_id'=> $user_id,'give'=>$give]);

        if ($user->amount <= $give['give_money']
            || $user->amount == 0) {
            return false;
        }
        if ($user_id) {
            $addCoins  = $this->api->addCoins($user_id, $give['give_money'] * 10000, 6);

            $getUserInfo = $this->api->getUserInfo($user_id);

            if (isset($addCoins['coins'])) {
                $data =[
                    'uid'   => $user_id,
                    'manager_id'    => 1,
                    'before_coins'  => $addCoins['coins'] + $addCoins['bank']  - $give['give_money'] * 10000,
                    'coins'   => $give['give_money'] * 10000,
                    'type'  => 10,
                    'remark'    => '活动自动赠送绑定金额',
                    'balance'   => $addCoins['bank'] + $addCoins['coins'],
                    'bank'  => $addCoins['bank'],
                    'ip'    => $getUserInfo['clientIP']?? '远程接口ip为空',
                ];
                ApiConis::create($data);
                User::where('id', 1)->decrement('amount', $give['give_money']);
                return true;
            }
            return false;
        }        
    }


}
