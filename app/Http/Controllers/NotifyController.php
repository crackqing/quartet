<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Log;
use App\Models\Game\Cashs;
use App\Service\Common\Wechat;

/**
 * Class NotifyController 支付回调处理,同步游戏订单
 * @package App\Http\Controllers
 */
class NotifyController extends BaseController
{

    /**
     * PayNotify function state=1成功，=2拒绝，-1表示同步给后台失败
     */
    public function notify()
    {
        $content = $this->paymentRsa(file_get_contents("php://input"),'decrypt');
        $requestContent = json_decode($content, true);
        $sign = $this->paymentSign($requestContent);
        if ($sign != $requestContent['sign'] 
            && $requestContent['tradestate'] != 'TRADE_SUCCESS') {
            Log::info('sign_error',['sign' => $sign,'requestContent' => $requestContent ]);
            return $this->failed('sign error');
        }
        // 1.代付的状态查询  transStatus 2 => 3(本四方的已到帐) ，3 => (6打款失败 回调游戏的处理) 。
        // 2 处理自己的操作状态更换 与访问远程的接口进行处理 更新游戏状态
        if ($requestContent['transStatus'] == 2) {
            #先同步到游戏服务器，在设置本地的DB字段 
            $cashs = Cashs::where('order_id',$requestContent['order_id'])->first();
            if ($cashs) {
                $result = $this->api->withdrawal_record($cashs->exchangeid,1);
                if ($result != 'failed') {
                    Cashs::where('order_id',$requestContent['order_id'])
                            ->update(['status'=>3]);
                    echo 'success';
                }

            }
        } elseif ($requestContent['transStatus'] == 3){
            $cashs = Cashs::where('order_id',$requestContent['order_id'])->first();
            if ($cashs) {
                $result = $this->api->withdrawal_record($cashs->exchangeid,2);
                if ($result != 'failed') {
                    Cashs::where('order_id',$requestContent['order_id'])
                            ->update(['status'=>6]);
                    echo 'success';
                }
            }
        }
        Log::info('content',['request' => $requestContent]);
    }

    /**    游戏下载页面
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shareUrl(Request $request)
    {
        $type = $request->type ?? 'df';
        $id = $request->id ??  base64_encode(80000);
        $version = $request->version == 1 ? 1 : 80000;
        $dfIpa = url('ipa/df.plist');
        $data =[
            'df'    => [
                'ios'   => 'itms-services://?action=download-manifest&url='.$dfIpa,
                'android'   => 'http://43.227.196.208:82/game/df.apk',
                'title' => '巅峰'
            ]
        ];
        if (isset($data[$type])) {
            $download = $data[$type];
        }
        return view('share',compact('download','id'));
    }

    public function test(Request $request)
    {
//        $to = 'y_yggdrasil@hotmail.com';
//        $subject = '邮件模板';
//        $message = 'test';
//        Mail::send('emails.test',['content' =>$message],function($message) use ($to,$subject){
//                $message->to($to)->subject($subject);
//        });


//        $wechat = new Wechat(); 不推荐写法，测试用.
//        $oauth = $wechat->easyWechat()->oauth;
//        if (empty($request->code)){
//
//            return $oauth->redirect('http://192.168.1.114:9008/test');
//        }
//        $user = $oauth->user();
////[2019-04-17 21:58:55] local.INFO: testWeixin=====> {"user":"[object] (Overtrue\\Socialite\\User: {\"id\":\"ovJNS5jMVwzN7kzPuAnNokgBtHZ0\",\"name\":\"xx\",\"nickname\":\"xxx\",\"avatar\":\"http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLdd9KX8dibSC0zxqrwEhmKmXpyfm7UNdE1HuDDZYmWZxkl2tv9oEmltkl8thknibHWFsHYMoUeE1Uw/132\",\"email\":null,\"original\":{\"openid\":\"ovJNS5jMVwzN7kzPuAnNokgBtHZ0\",\"nickname\":\"xx\",\"sex\":1,\"language\":\"zh_CN\",\"city\":\"深圳\",\"province\":\"广东\",\"country\":\"中国\",\"headimgurl\":\"http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLdd9KX8dibSC0zxqrwEhmKmXpyfm7UNdE1HuDDZYmWZxkl2tv9oEmltkl8thknibHWFsHYMoUeE1Uw/132\",\"privilege\":[]},\"token\":\"20_DY9eVCav_22tR5Z1aPyk-7eKE5fkElZk5cdB89evT5owpXh70zziKeHAb0G_sMlteP9MKU45E0bEB8vnv5pJzg\",\"provider\":\"WeChat\"})","request":{"code":"0210dExb0GDnlA1G3tvb0x5Hxb00dExT","state":"4f495f9e1b7ec195f47b4a0304b73715"}}
//        Log::info('testWeixin=====>',['user'=>$user,'request'=> $request->all()]);
//
        //容器实例, 绑定区分。 不在具体控制下面new 
        dd($this->app->make('EasyWechat'));

        $wechat = resolve('EasyWechat');
        $array = [
            'scopes'   => ['snsapi_userinfo'],
            'callback' => '/test'
        ];
//        $wechat->setConfig($array);  根据不同的配置,调用不同的方法.
        dd($wechat->getConfig());


    }

}
