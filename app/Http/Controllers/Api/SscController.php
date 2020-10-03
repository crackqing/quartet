<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

use Log;
class SscController extends BaseController
{
    public $client;

    public $ssc_url ='http://192.168.1.105:7003/Public.gameLoginDo.do';

    public $client_ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1';
    /**
     * 游戏前端登录时---判断当前用户 function
            $this->client = new Client();
            $options = [
                'form_params' => [
                    'user_id'   => $user['uid'],
                    'nickname'   => $user['nickname'],
                    'clientIP'   => $user['clientIP'],
                    'regtime'   => $user['regtime']
                ],
                'headers'   => [
                    'User-Agent' => $this->client_ua,
                ],
                'timeout'   => 10,
            ];
            $response = $this->client->request('POST',$this->ssc_url,$options);
            if ($response->getStatusCode() == 200) {
                $body = $response->getBody()->getContents();
                $body = json_decode($body,true);
     * @param Request $request
     * @return void
     */
    public function user(Request $request)
    {
        $user_id = $request->user_id;
        $session_id = $request->session_id;

        $user = $this->api->getUserInfo($user_id);

        try {
            if ($this->parse($user['session'])
            && $user['session'] == $session_id
            && $user['status'] == 0) {
                $data= [
                    'user_id'    => $user['uid'],
                    'nickname'    => $user['nickname'],
                    'clientIP'    => $user['clientIP'],
                    'regtime'    => $user['regtime'],
                    'message' => '登录成功'
                ];

                Log::info('ssc___user___',['user'=> $user,'user_id'=>$user_id,'session_id' => $session_id]);

                return $this->success($data);
            }
        } catch (\Exception $e) {
            $data= [
                'message' => '登录失败或帐号被锁定或银行钱数小于0'
            ];
            return $this->failed($data);
        }
    }
    /**
     * 游戏余额---默认是显示银行的数值. 扣除也单独使用银行的接口 function
     *
     * @param Request $request
     */
    public function userBank(Request $request)
    {
        $user_id = $request->user_id;
        $user = $this->api->getUserInfo($user_id);

        try {
            if ($user['status'] == 0) {
                $data = [
                    'bank' => (int) $user['bank'] / 10000,
                    'message'   => '查询成功',
                ];
                Log::info('ssc_____userBank_____',['data'=>$data]);
                return $this->success($data);
            }
        } catch (\Exception $e) {
            $data= [
                'bank' => 0,
                'message' => '银行钱数小于0'
            ];
            return $this->failed($data);
        }
    }
    
    /**
     * 用户扣除 ---失败则进入队列. 防止下注成功后并没有扣除 function
     */
    public function userDeduction(Request $request)
    {
        $user_id = $request->user_id;
        $money = $request->money * 10000;
        $result = $this->api->addCoins($user_id,-$money);

        if ($result != 'failed') {

            Log::info('ssc_deduction_success',['user_id'=>$user_id,'money'=>$money]);

            return $this->success('成功扣除用户银行数钱');
        }
        //队列操作 或者日志记录
        Log::info('ssc_deduction_exception',['user_id'=>$user_id,'money'=>$money]);
    }


    /**
     * 游戏内的登录验证SESSION function
     *
     * @param [type] $sessionID
     * @return void
     */
    private function parse($sessionID)
    {
        if (!$sessionID) {
            return false;
        }
        $string = base64_decode($sessionID);
        if ($string === false) {
            return false;
        }
        $data = explode('_', $string);
        $sign = array_pop($data);
        if ($sign != $this->makeSign($data)) {
            return false;
        }
        $expire = (int)$data[0];
        if ($expire < time()) {
            return false;
        }
        $this->uid = (int)$data[1];
        $this->nickname = $data[2];
        $this->platform = (int)$data[3];
        $this->regtime = (int)$data[4];
        $this->version = $data[5];
        // Log::info('pars=================');
        return true;
    }
    private function makeSign($data)
    {
        return md5(implode('|', $data).'_'. '2b2y2');
    }
}
