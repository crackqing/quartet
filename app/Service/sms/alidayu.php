<?php 

namespace App\Service\sms;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;


use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Flc\Alidayu\Requests\IRequest;


/**
 * 前锋棋牌 每个应用对应的APPID 与APPKEY
 */
class alidayu
{
	public $appid = '1400082884';
	public $appkey= '36886a17b1e043ed68118df9064495ba';

    public $client ;

    public $req;

	public function __construct()
	{
        $config = [
            'app_key'   => $this->appid,
            'app_secret'    => $this->appkey,
        ];

        $this->client = new Client(new App($config));
        $this->req    = new AlibabaAliqinFcSmsNumSend;
        
	}

	public function success(Int $phone,Array $data,Int $templateID )
	{
		$this->templateSMS($phone,$data,$templateID = 107814);
	}


	public function unPass(Int $phone,Array $data,Int $templateID )
	{
		$this->templateSMS($phone,$data,$templateID = 107808);
	}

	public function lock(Int $phone,Array $data,Int $templateID )
	{
		$this->templateSMS($phone,$data,$templateID = 107812);
	}

	public function lockRestore(Int $phone,Array $data,Int $templateID )
	{
		$this->templateSMS($phone,$data,$templateID = 107807);
	}

	public function failureAudit(Int $phone,Array $data,Int $templateID  )
	{
		$this->templateSMS($phone,$data,$templateID =107811);
	}

	public function authCode(Int $phone,Array $data,Int $templateID ,Int $authPhone = 1)
	{
		return $this->templateSMS($phone,$data,$templateID = 107805);
	}

	public function resetPasswd(Int $phone,Array $data,Int $templateID)
	{
		return $this->templateSMS($phone,$data,$templateID =118901);
	}
	public function activitySms(Int $phone,Array $data,Int $templateID)
	{
		return $this->templateSMS($phone,$data,$templateID =139806);
	}

	public function activetySmsCaptcha(Int $phone,Array $data,Int $templateID)
	{
		return $this->templateSMS($phone,$data,$templateID =145912);
	}

	public function activetySmsCaptcha2(Int $phone,Array $data,Int $templateID)
	{
		return $this->templateSMS($phone,$data,$templateID =167560);
	}
	public function templateSMS(Int $phone,Array $data,Int $templateID = 107805 ,Int $authPhone = 0)
	{
		//默认为发送验证码
		if ($templateID == 107805) {
            return false;
		} elseif ($templateID == 145912 || $templateID == 167560){
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
	        $code = rand(100000,999999);

	        $send = $this->tencent->sendWithParam("86",$phone,$templateID,[$code],"","","");

            $this->req->setRecNum($phone)
                      ->setSmsParam([
                        'number' => $code
                    ])
                    ->setSmsFreeSignName('四方')
                    ->setSmsTemplateCode('11445555');
            $resp = $this->client->execute($this->req);

            Log::info('alidayu',['resp'=>$resp,'code'=>$code]);

            $state['status'] ='success';
            $state['code'] ='200';
            $state['data']['captcha'] = $code;

    		return response()->json($state);
		}  else {
			// $send = $this->tencent->sendWithParam("86",$phone,$templateID,$data,"","","");
        }
        // $result = json_decode($send,true);
        // if($result['errmsg'] != 'OK'){
        //     Log::error('Tencent_Send failed!', ['phone'=>$phone, 'resp'=>$result]);
        //     return Config::get('error.10102');
        // }
        // return Config::get('error.0');
	}

}

