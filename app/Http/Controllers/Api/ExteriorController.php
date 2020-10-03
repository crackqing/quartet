<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests\Game\OrderSyncRequest;
use App\Http\Requests\Game\CashRequest;
use App\Http\Requests\Game\BindInviteCodeRequest;
use App\Models\Game\Orders;
use App\Models\Game\Cashs;

use App\User;
use Cache;
use Config,Log;
use Illuminate\Support\Facades\Redis;
use App\Models\Server\GameSettings;
use App\Models\Game\Settings;
/**
 * 涉及订单同步，兑换同步。 代理绑定，申请 ==，手机验证码 class
 *
 * 1.增加Header头为 Accept  application/json
 */
class ExteriorController extends BaseController
{
    /**
    * @api {POST} /api/v1/orderSync 订单Sync
    * @apiGroup 同步数据===>Syncs
    * @apiVersion 1.0.0
    * @apiDescription 订单Sync
    * @apiParam (参数) {Number} price 充值金额,单位为分
    * @apiParam (参数) {String} order_id 充值的商户订单号
    * @apiParam (参数) {Number} agent_id 代理ID,相当于玩家UID
    * @apiParam (参数) {String} pay_type zfb,wx,unipony,qq ==
    * @apiParam (参数) {Number} coins 玩家当前的余额金币
    * @apiParam (参数) {Number} bank 玩家当前的银行数钱
    *
    * @apiParamExample {json} Request-Example:
    * {
    *     "price" : "600",
    *     "order_id" : "order-xxxxx1111",
    *     "agent_id" : "3000xx",
    *     "pay_type" : "zfb",
    *     "coins" : "10000",
    *     "bank" : "0",    *
    * }
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": "success",
    *      "code": 200,   | 1001 为订单重复
    *      "message": "订单同步成功"
    *  }
    */
    public function orderSync(OrderSyncRequest $request)
    {
        $data = $request->all();
        $orders =  Orders::where('order_id', $data['order_id'])->first();
        
        if ($orders) {
            return $this->nativeRespond(1001, ['message' =>  '订单重复'], 'success');
        }

        $orders = Orders::create($data);

        if ($orders) {
            return $this->message('订单同步成功');
        }
    }

    /**
    * @api {POST} /api/v1/cashSync 兑换Sync
    * @apiGroup 同步数据===>Syncs
    * @apiVersion 1.0.0
    * @apiDescription 兑换Sync
    * @apiParam (参数) {Number} cash_money 兑换金钱 (以分为单位.)
    * @apiParam (参数) {Number} agent_id 代理ID,相当于玩家UID
    * @apiParam (参数) {String} agent_nickname 玩家名称
    * @apiParam (参数) {Number} coins 玩家当前的余额金币
    * @apiParam (参数) {Number} bank 玩家当前的银行数钱
    * @apiParam (参数) {String} exchangeType  兑换方式 1:支付宝,2:银行,3:微信
    * @apiParam (参数) {String} realname 玩家名字
    * @apiParam (参数) {String} account 兑换到的账号
    *
    * @apiParamExample {json} Request-Example:
    * {
    *     "cash_money" : "600",
    *     "agent_id" : "3000xx",
    *     "agent_nickname" : "nick",
    *     "coins" : "10000",
    *     "bank" : "0",
    *     "exchangeType" : "zfb",
    *     "realname" : "realname",
    *     "account" : "account",
    * }
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": "success",
    *      "code": 200,
    *      "message": "订单同步成功"
    *  }
    */
    public function cashSync(CashRequest $request)
    {
        $data = $request->all();

        $cash = Cashs::create($data);
        #兑换通知消息处理 jobs
        if ($cash) {
            return $this->message('兑换同步成功');
        }
        return $this->failed('兑换失败');
    }

    /**
    * @api {POST} /api/v1/bindInviteCode 绑定邀请码
    * @apiGroup 绑定操作===>Agent
    * @apiVersion 1.0.0
    * @apiDescription 绑定邀请码
    * @apiParam (参数) {Number} bind_id 绑定ID (80001) 五位ID
    * @apiParam (参数) {Number} mobile 手机号码
    * @apiParam (参数) {Number} agent_id 玩家UID
    * @apiParam (参数) {String} agent_nickname 玩家名称
    *
    * @apiParamExample {json} Request-Example:
    * {
    *     "bind_id" : "600",
    *     "mobile" : "3000xx",
    *     "agent_id" : "nick",
    *     "agent_nickname" : "nick",
    * }
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": "success",
    *      "code": 200,
    *      "message": "绑定成功"
    *  }
    */
    public function bindInviteCode(BindInviteCodeRequest $request)
    {
        $data = $request->all();
        #存在并是管理的后台的人员才能绑定. 不然提示错误
        $user =  User::where('email', $data['bind_id'])->where('manager', 1)->first();
        if (!$user) {
            return $this->nativeRespond(2001, ['message' => '不存在的绑定ID,只能绑定代理ID'], 'error');
        }
        $dataInsert = [
            'bind_id'   => (int) $data['bind_id'],
            'mobile'    => $data['mobile'],
            'agent_id'  => $data['agent_id'],
            'agent_nickname'    => $data['agent_nickname'],
            'name'  => '用户信息',
            'email' => $data['agent_id'],
            'manager'   => 0,
            'password'  => bcrypt('Dtest1123456$'),
        ];
        $user = User::where('email', $data['agent_id'])->first();
        Log::info('bindInviteCode====>',['data' => $data,'user' => $user]);
        
        if ($user) {
            $id = User::where('email', $data['agent_id'])->update($dataInsert);
            #触发赠送的任务队列，也可以写成trais
            $this->bindGiveHandle($data['agent_id']);
        } else {
            $id =  User::insert($dataInsert);
        }
        return $id ? $this->message('绑定成功') : $this->nativeRespond(2002, [], 'error');
    }

    /**
    * @api {POST} /api/v1/checkBindStatus 检测uid绑定状态
    * @apiGroup 绑定操作===>Agent
    * @apiVersion 1.0.0
    * @apiDescription 检测uid绑定状态
    * @apiParam (参数) {Number} bind_id 玩家UID
    *
    * @apiParamExample {json} Request-Example:
    * {
    *     "bind_id" : "300001",

    * }
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": "success",
    *      "code": 200,  |  2001 不存在的绑定ID
    *      "message": "已绑定"
    *  }
    */
    public function checkBindStatus(Request $request)
    {
        $data = $request->all();
        $user =  User::where('email', $data['bind_id'])->first();
        if (!$user) {
            return $this->nativeRespond(2001, ['message' => '不存在的绑定ID'], 'error');
        }
        
        return $this->success('已绑定');
    }


    /**
    * @api {POST} /api/v1/checkBindExist 检测邀请码是否存在
    * @apiGroup 绑定操作===>Agent
    * @apiVersion 1.0.0
    * @apiDescription 检测邀请码是否存在
    * @apiParam (参数) {Number} agent_id 代理ID
    *
    * @apiParamExample {json} Request-Example:
    * {
    *     "agent_id" : "80000",

    * }
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": "success",
    *      "code": 200,  |  2001 不存在的绑定ID
    *      "message": "已绑定"
    *  }
    */
    public function checkBindExist(Request $request)
    {
        $data = $request->all();
        $user =  User::where('email', $data['agent_id'])
                        ->where('manager',1)
                        ->first();
        if (!$user) {
            return $this->nativeRespond(2001, ['message' => '邀请码不存在!'], 'error');
        }
        return $this->success('邀请码存在^_^');
    }


    /**
    * @api {POST} /api/v1/statistics 统计(日活,新增,在线)
    * @apiGroup 统计===>statis
    * @apiVersion 1.0.0
    * @apiDescription 登录统计 (直接发送,不用管返回处理.)
    * @apiParam (参数) {Number} uid 用户ID
    * @apiParam (参数) {string} enum 类型(登录为login,新增为new,实时在线为online)
    * @apiParam (参数) {string} agent_nickname 玩家名称
    * @apiParamExample {json} Request-Example:
    * {
    *     "uid" : "1111xx"
    *     "enum" : "1111xx",
    *     "agent_nickname" : "string....",
    *
    * }
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "message" : 'sucess'
    *  }
    */
    public function statistics(Request $request)
    {
        $data = $request->all();
        $uid = $data['uid'];
        $enum = $data['enum'];
        $nickName = $data['agent_nickname'] ?? '未知名称';

        #新增也算日活，两个一起记。只是与新增的字段不同
        switch ($enum) {
            case 'login':
                #用于做月活跃的玩家操作.
                $dataLogin = date('Ymd').':';
                Redis::command('setbit', [$dataLogin,$uid,1]);
                    #总的数据存放每天,月活上面是日活
                break;
            #游客登录 ，绑定在使用其它的值. bind
            case 'new':  #存放缓存队列之中
                $dataInsert = [
                    'bind_id'   => 80000,
                    'agent_id'  => $data['uid'],
                    'agent_nickname'    => $nickName,
                    'name'  => $nickName,
                    'email' => $data['uid'],
                    'manager'   => 0,
                    'password'  => bcrypt('Dtest1123456$'),
                ];
                User::where('agent_id', $data['uid'])->updateOrCreate($dataInsert);
                #新增也算到日活上.$data
                $newData = date('Ymd').'new';
                $dataLogin = date('Ymd').':';

                Redis::command('setbit', [$dataLogin,$uid,1]);
                Redis::command('setbit', [$newData,$uid,1]);
                break;
            case 'online':
                return '';
                break;
            default:
            return $this->message('错误的enum类型');
                
                break;
        }
        return $this->success('success');

        #查找当天日活数为 bitcount 20180730

        #次留,bitop进行与或运算 xor,and,or bitop and 20180730and20180731  20180730 20180731
    }
    /**
    * @api {POST} /api/v1/vipSeting VIP配置
    * @apiGroup 支付配置===>pay
    * @apiVersion 1.0.0
    * @apiDescription VIP配置
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "data" : []
    *  }
    */
    public function vipSeting()
    {
        $vipChannel = Cache::get('gold:vipchannels');
        $data = [];

        foreach ($vipChannel as $v) {
            if (empty($v)) {
                continue;
            }

            $data[] = $v;
        }
        return $this->success($data);
    }

    /**
    * @api {POST} /api/v1/paySeting 支付配置
    * @apiGroup 支付配置===>pay
    * @apiVersion 1.0.0
    * @apiDescription 支付配置
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "data" : []
    *  }
    */
    public function paySeting()
    {
        $vipChannel = Cache::get('gold:paychannels');
        $data  =[];
        foreach ($vipChannel as $v) {
            if ($v['status'] == 1) {
                $data[] = $v;
            }
        }
        return $this->success($data);
    }

    /**
    * @api {POST} /api/v1/cashSeting 兑换配置
    * @apiGroup 支付配置===>pay
    * @apiVersion 1.0.0
    * @apiDescription 兑换配置
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "data" : []
    *  }
    */
    public function cashSeting()
    {
        $vipChannel = Cache::get('gold:cashchannels');
        $data  =[];
        foreach ($vipChannel as $v) {
            if ($v['status'] == 1) {
                $data[] = $v;
            }
        }
        return $this->success($data);
    }

    /**
    * @api {POST} /api/v1/activetySmsCaptcha 短信验证码
    * @apiGroup 第三方===>thirdCall
    * @apiVersion 1.0.0
    * @apiParam (参数) {Number} phone 手机号码1开头3-9 号段支付,13位。
    * @apiDescription 短信 10分内单个手机可发送3次，防止恶意调用.^_^
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "data" : ['captcha' => 'XXXX']
    *  }
    */
    public function activetySmsCaptcha(Request $request)
    {
        $param = $request->all();
        $appName = Config::get('app.name'); //根据APP_NAME 调用对应渠道

        if (preg_match("/^1[3456789]\d{9}$/", $param['phone'])) {
                return  $this->sms->activetySmsCaptcha($param['phone'], [], 1,$appName) ;
        }            
        return $this->failed('failed');
    }

    /**
    * @api {POST} /api/v1/qrcodeImages 二维码
    * @apiGroup 支付配置===>pay
    * @apiVersion 1.0.0
    * @apiParam (参数) {Number} user_id 用户ID 查找绑定上级返回的二维码生成
    * @apiDescription 二维码--直接访问 https://ssl.dfylpro.com/ 苹果安装
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "data" : ["path":'http://xxxxxxx/xxx.png']
    *  }
    */
    public function qrcodeImages(Request $request)
    {
        $param = $request->all();
        $projectUrl = Config::get('app.url');

        $version = Config::get('services.platform_str');

        $url = $projectUrl.'/qrcode/80000.png' .$version;

        if (isset($param['user_id']) ) {
            $projectName = Config::get('app.name');

            $public = public_path('qrcode');
            $bind_id = User::where('agent_id',$param['user_id'])->value('bind_id');

            $bind_id && $url = $projectUrl.'/qrcode/'.$bind_id.'.png' .$version;
            $data =[
                'path'  => $url
            ];
            return $this->success($data);
        } 
        $data =[
            'path'  => $url
        ];
        return $this->success($data);
    }
    /**
    * @api {POST} /api/v1/inviteSw 二维码(开关)
    * @apiGroup 绑定操作===>Agent
    * @apiVersion 1.0.0
    * @apiDescription 二维码绑定功能开关状态判断
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "data" : ['switch' => 1 or 0]
    *  }
    */
    public function inviteSw(Request $request)
    {
        $settings = Settings::where('key','sm_auto_bind')->select('value')->first();
        
        $data =[
            'switch'    => $settings->value
        ];

        return $this->success($data);
    }
    /*============================游戏大厅管理======================================*/
    /**
    * @api {POST} /api/v1/gameHallKindid 获取游戏桌子数据
    * @apiGroup 游戏管理===>HallKindid
    * @apiVersion 1.0.0
    * @apiDescription 获取对应的游戏桌子数据（后台编辑）
    * @apiParam (参数) {Number} kindid 游戏ID
    * @apiSuccessExample Success-Response:
    *  {
    *      "status": success,
    *      "code": '200',
    *      "data" : ['captcha' => '356789']
    *  }
    */
    public function gameHallKindid(Request $request)
    {
        $data = $request->all();

        $settings =  GameSettings::where('kindid',$data['kindid'])->get();

        return $this->success($settings);
        
    }
    /*============================游戏大厅管理======================================*/
    /**
     * @api {POST} 状态码说明,参考对应接口的错误码
     * @apiGroup 状态码===>errorCodeStats
     * @apiVersion 1.0.0
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 Response Code
     *     {
     *      "status": "success", |failed  错误只会返回 success 与 failed
     *      "code": 200,
     *      "message": "已绑定"
     *
     *         200  ---- {{ 只要为200都是成功的操作 }}
     *         400  ---- {{  400 http error  }}
     *         403  ---- {{  403 no Permission  }}
     *         404  ---- {{  404 no found   }}
     *         500  ---- {{  500 服务器错误 }}
     *         1001 ---- {{ 为订单重复 }}
     *         2001 ---- {{ 不存在的绑定ID }}
     *         2002 ---- {{ 重复添加 }}
     *     }
     */
    public function errCode()
    {
    }

}
