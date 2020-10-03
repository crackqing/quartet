<?php

namespace App\Service\sms;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Overtrue\EasySms\EasySms;

/**
 * 前锋棋牌 每个应用对应的APPID 与APPKEY
 */
class chuanglan
{
    public $client ;

    public $sendSms;

	public $thrid = 'common';

    public function success(Int $phone, array $data, Int $templateID)
    {
        $this->templateSMS($phone, $data, $templateID = 107814);
    }


    public function unPass(Int $phone, array $data, Int $templateID)
    {
        $this->templateSMS($phone, $data, $templateID = 107808);
    }

    public function lock(Int $phone, array $data, Int $templateID)
    {
        $this->templateSMS($phone, $data, $templateID = 107812);
    }

    public function lockRestore(Int $phone, array $data, Int $templateID)
    {
        $this->templateSMS($phone, $data, $templateID = 107807);
    }

    public function failureAudit(Int $phone, array $data, Int $templateID)
    {
        $this->templateSMS($phone, $data, $templateID =107811);
    }

    public function authCode(Int $phone, array $data, Int $templateID, Int $authPhone = 1)
    {
        return $this->templateSMS($phone, $data, $templateID = 107805);
    }

    public function resetPasswd(Int $phone, array $data, Int $templateID)
    {
        return $this->templateSMS($phone, $data, $templateID =118901);
    }
    public function activitySms(Int $phone, array $data, Int $templateID)
    {
        return $this->templateSMS($phone, $data, $templateID =139806);
    }

    public function activetySmsCaptcha(Int $phone, array $data, Int $templateID,String $thrid = 'common')
    {
		return $this->templateSMS($phone, $data, $templateID =145912,0,$thrid);
    }

    public function activetySmsCaptcha2(Int $phone, array $data, Int $templateID)
    {
        return $this->templateSMS($phone, $data, $templateID =167560);
	}

    public function templateSMS(Int $phone, array $data, Int $templateID = 107805, Int $authPhone = 0, $thrid = '')
    {
		$code = rand(1000, 9999);
        //默认为发送验证码
        if ($templateID == 107805) {
            return false;
        } elseif ($templateID == 145912 || $templateID == 167560) {

			$huiyiAppid = 'C01913636';
			$huiyiAppKey = '7787d561808ec499c8ce5f0cfd1f9fd3';

			$content = "您的验证码为：{$code}。";

			if ($thrid == 'tiantian') {
				$huiyiAppid = 'C76535373';
				$huiyiAppKey = '2edafd27bb1a99796abc53a23d9e6810';
				
				$content = "您的验证码是：{$code}。请不要把验证码泄露给其他人。";
			}


            #10分内，只能发送三次....
            $redis = Redis::connection('default');
            $num = $redis->get('activetySmsCaptcha'.$phone);
            if (!$num) {
                $redis->setex('activetySmsCaptcha'.$phone, 60 * 10, 1);
            } else {
                if ($num > 3) {
                    Log::error('send too frequently', ['phone'=>$phone]);
                    return response()->json(Config::get('resjson.4002'));
                }
                $redis->incr('activetySmsCaptcha'.$phone);
            }
            try {
                $config = [
                    // HTTP 请求的超时时间（秒）
                    'timeout' => 3.0,
                
                    // 默认发送配置
                    'default' => [
                        // 网关调用策略，默认：顺序调用
                        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                        // 默认可用的发送网关
                        'gateways' => [
                            'huyi'
                        ],
                    ],
                    // 可用的网关配置
                    'gateways' => [
                        'errorlog' => [
                            'file' => '/data/php/quartet/storage/logs/easy-sms.log',
                        ],
                        // 'chuanglan' => [
                        //     'account' => 'CN5366413',
                        //     'password' => 'aFYgXVHo5b9076',
                        //     'channel'  => \Overtrue\EasySms\Gateways\ChuanglanGateway::CHANNEL_VALIDATE_CODE,
                        //     'sign' => '【巅峰集团】',
                        //     'unsubscribe' => '回TD退订',
                        // ],
                        'huyi'	=> [
                            'api_id'	=> $huiyiAppid,
                            'api_key'	=> $huiyiAppKey
                        ]
                    ],
                ];
                $easySms = new EasySms($config);
                $easySms->send($phone, [
                    'content'  =>  $content,
                    'template' => '',
                    'data' => [
                        'code' => $code,
                    ],
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                Log::info('error==>sms====>', ['sms'=> $exception->getResults()]);
            }
            $state['status'] ='success';
            $state['code'] ='200';
            $state['data']['captcha'] = $code;

            return response()->json($state);
        } else {
        }

    }
}
