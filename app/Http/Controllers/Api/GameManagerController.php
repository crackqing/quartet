<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Game\RecordDetailEvery;

use App\Http\Resources\RecorStatisEvery;
use App\Models\Game\RecordDetail;
use DB;
use Config;
use Cache;
use Log;
use Validator;

use App\Models\Game\Cashs;
use App\Service\Paytem\Http;
use App\Service\ImageUploadHandler;
use App\Models\Server\Images;
use App\User;
use League\Flysystem\Adapter\NullAdapter;
use App\Models\Server\ApiConis;
use App\Models\Game\Orders;

/**
 * 游戏管理 ----记录
 */
class GameManagerController extends BaseController
{
    public function index(Request $request)
    {
        #分页数量  与搜索条件 (默认搜索的是总)
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $kindid = $request->kindid ?? false;
        $tid = $request->tid ?? false;
        #kindid 等于10000的情况下 处理...
        if ($kindid == 10000) {
            $kindid = false;
        }
        $RecordDetailEveryTotal = RecordDetailEvery::select(
            DB::raw('SUM(yazhu) as yazhu'),
            DB::raw('SUM(defen) as defen'),
            DB::raw('SUM(choushui) as choushui'),
            'start_time',
            'end_time'
            )
            ->orderBy('id', 'DESC');
        //默认为所有的搜索条件
        if ($kindid || $tid) {
            $everyFive = RecordDetailEvery::orderBy('id', 'DESC');
        } else {
            $everyFive = RecordDetailEvery::select(
                DB::raw('SUM(yazhu) as yazhu'),
                DB::raw('SUM(defen) as defen'),
                DB::raw('SUM(choushui) as choushui'),
                'start_time',
                'end_time'
                )
                ->orderBy('id', 'DESC')->groupBy('start_time', 'end_time');
        }
        if ($time) {
            $time = explode(' - ', $time);
            $everyFive = $everyFive->whereBetween('start_time', [$time[0],$time[1]]);
            $RecordDetailEveryTotal = $RecordDetailEveryTotal->whereBetween('start_time', [$time[0],$time[1]]);
        }
        #保持只有一个的总显示.
        if ($kindid xor $tid) {
            $everyFive = $everyFive->select(
                DB::raw('SUM(yazhu) as yazhu'),
                DB::raw('SUM(defen) as defen'),
                DB::raw('SUM(choushui) as choushui'),
                'start_time',
                'end_time',
                'kindid'
                )->where('kindid', $kindid)->groupBy('kindid', 'start_time', 'end_time');
            
            $RecordDetailEveryTotal = $RecordDetailEveryTotal->where('kindid', $kindid);
        } elseif ($tid xor $kindid) {
            $everyFive = $everyFive->where('tid', $tid);
            $RecordDetailEveryTotal = $RecordDetailEveryTotal->where('tid', $tid);
        } elseif ($kindid && $tid) {
            $everyFive = $everyFive->where('kindid', $kindid)->where('tid', $tid);
            $RecordDetailEveryTotal = $RecordDetailEveryTotal->where('kindid', $kindid)->where('tid', $tid);
        }
        $RecordDetailEveryTotal = $RecordDetailEveryTotal->get();
        
        $everyFive = $everyFive->paginate($paginate);
        return (new RecorStatisEvery($everyFive))
                ->additional(['meta' => [
                    'total_data' => $RecordDetailEveryTotal,
                    ]]);
        ;
    }
    /**
     * 游戏记录总的显示 function
     *
     * @param Request $request
     * @return void
     */
    public function gameRecordTotal(Request $request)
    {
        #分页数量  与搜索条件 (默认搜索的是总)
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;

        $everyFive = RecordDetailEvery::select(
            DB::raw('SUM(yazhu) as yazhu'),
            DB::raw('SUM(defen) as defen'),
            DB::raw('SUM(choushui) as choushui'),
            'start_time',
            'end_time'
            )
            ->orderBy('id', 'DESC');
        //时间搜索
        if ($time) {
            $everyFive = $everyFive->whereBetween('created_at', [$time[0],$time[1]]);
        }

        $everyFive = $everyFive->groupBy('start_time', 'end_time')->paginate($paginate);

        return (new RecorStatisEvery($everyFive));
    }

    /**
     * 游戏管理--记录详情 function
     *
     * @param Request $rqueste
     * @return void
     */
    public function gameRecordDetail(Request $request)
    {
        #分页数量  与搜索条件 (默认搜索的是总)
        $paginate = $request->paginate ?? 200;
        $time = $request->time ?? false;
        $keyword = $request->keyword ?? false;
        $user_id = $request->user_id ?? false;
        $kindid = $request->kindid ?? false;

        $RecordDetail =RecordDetail::select(
            'time',
            'qishu',
            'uid',
            'kindid',
            'yazhu',
            'defen',
            'yingli',
            'choushui',
            'isdealer',
            'xiazhu',
            'xiazhupx',
            'kaijiangpx'
        )
        ->when($user_id, function ($query) use ($user_id) {
            return $query->where('uid', $user_id);
        })
        ->when($kindid, function ($query) use ($kindid) {
            if ($kindid == 10000) {
                return $query;
            }
            $kindidAttr = implode(',',$kindid);
            
            return $query->whereRaw('FIND_IN_SET(kindid,?)',[$kindidAttr]);
        })
        ->when($time, function ($query) use ($time) {
            $time = explode(' - ', $time);
            return $query->whereBetween('time', [$time[0],$time[1]]);
        })
        ->where('kindid','!=',9)
        ->orderBy('time', 'DESC');

        return (new RecorStatisEvery($RecordDetail->paginate($paginate)));
    }

    /**
     * 游戏管理--记录详情 function
     *
     * @param Request $rqueste
     * @return void
     */
    public function gameFangKaRecordDetail(Request $request)
    {
        #分页数量  与搜索条件 (默认搜索的是总)
        $paginate = $request->paginate ?? 10;
        $time = $request->time ?? false;
        $keyword = $request->keyword ?? false;
        $user_id = $request->user_id ?? false;
        
        $RecordDetail =RecordDetail::select(
            'time',
            'qishu',
            'uid',
            'kindid',
            'yazhu',
            'defen',
            'yingli',
            'choushui',
            'isdealer',
            'xiazhu',
            'xiazhupx',
            'kaijiangpx'
        )
        ->when($user_id, function ($query) use ($user_id) {
            return $query->where('uid', $user_id);
        })
        ->when($time, function ($query) use ($time) {
            $time = explode(' - ', $time);
            return $query->whereBetween('time', [$time[0],$time[1]]);
        })
        ->where('kindid','<=',999)
        ->orderBy('time', 'DESC');

        return (new RecorStatisEvery($RecordDetail->paginate($paginate)));
    }


    /**
     * 游戏记录--图形显示 只有时间搜索显示 function
     *
     * @param Request $request
     * @return void
     */
    public function gameRecordCharts(Request $request)
    {
        $time = $request->time ?? false;
        $everyFive = RecordDetailEvery::orderBy('id', 'ASC');
        $today = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 00:00:00', strtotime('+1 day'));
        //时间搜索 默认的时间为今天数据.可以根据时间生成charts所需数据    单个游戏，单个桌子的情况
        if ($time) {
            $everyFive = $everyFive->whereBetween('created_at', [$time[0],$time[1]]);
        } else {
            $everyFive = $everyFive->whereBetween('created_at', [$today,$todayEnd]);
        }
        $everyFive = $everyFive->groupBy('kindid', 'tid', 'start_time')->get();
        $data = [];
        foreach ($everyFive as $k => $v) {
            $data[$v->start_time]['date'] = substr($v->start_time, 11, 5) ;
            $data[$v->start_time]["$v->kindid-$v->tid"] =$v->yazhu - $v->defen;
            
            // echo $v->start_time.'======>'.$v->kindid.'=======>'.$v->tid.'======>'.$v->yazhu.'<br/>';
        }
        $dataClear = [];
        foreach ($data as $k => $v) {
            $dataClear[] = $v;
        }

        return $this->success($dataClear);
    }

    /**
     * 返回登录公告_代理公告_跑马灯 function
     *
     * @return void
     */
    public function notice()
    {
        if (!Cache::has('gm:gameLoginNotice')) {
            Cache::forever('gm:gameLoginNotice', '游戏登录公告');
        }
        if (!Cache::has('gm:agentLoginNotice')) {
            Cache::forever('gm:agentLoginNotice', '代理公告');
        }
        if (!Cache::has('gm:marqueeLoginNotice')) {
            Cache::forever('gm:marqueeLoginNotice', '跑马灯');
        }

        $images = Images::where('type', 'qrcode')->orderBy('id', 'DESC')->first();

        $data = [
            'gameLoginNotice'   => Cache::get('gm:gameLoginNotice'),
            'agentLoginNotice'  => Cache::get('gm:agentLoginNotice'),
            'marqueeLoginNotice'    => Cache::get('gm:marqueeLoginNotice'),
            'images'    => $images->path ?? '',
        ];
        return $this->success($data);
    }

    public function agentLoginNotice(Request $request)
    {
        $params  = $request->all();
        $content = $params['content'] ?? '代理公告';

        Cache::forever('gm:agentLoginNotice', $content);
        
        return $this->success($content);
    }
    public function gameLoginNotice(Request $request)
    {
        $params  = $request->all();

        $content = $params['content'] ?? '游戏登录公告';

        Cache::forever('gm:gameLoginNotice', $content);
        
        return $this->success($content);
    }

    public function marqueeLoginNotice(Request $request)
    {
        $params  = $request->all();

        $content = $params['content'] ?? '跑马灯';

        Cache::forever('gm:marqueeLoginNotice', $content);
        
        $this->api->addSysMessage($content);

        return $this->success($content);
    }

    public function vipSeting(Request $request)
    {
        if (!Cache::has('gold:vipchannels')) {
            Cache::forever('gold:vipchannels', Config::get('vipchannels'));
        }
        return $this->success(Cache::get('gold:vipchannels'));
    }

    public function paySeting(Request $request)
    {
        if (!Cache::has('gold:paychannels')) {
            Cache::forever('gold:paychannels', Config::get('paychannels'));
        }
        return $this->success(Cache::get('gold:paychannels'));
    }
    /**
     * 兑换通道配置--支付宝与银联 function
     *
     * @param Request $request
     * @return void
     */
    public function cashSeting(Request $request)
    {
        if (!Cache::has('gold:cashchannels')) {
            Cache::forever('gold:cashchannels', Config::get('cashChannel'));
        }
        return $this->success(Cache::get('gold:cashchannels'));
    }

    public function vipSetingUpdate(Request $request)
    {
        $data = $request->all() ?? false; //数组格式所有，更新所有。
        Cache::forever('gold:vipchannels', $data);
        return $this->success(Cache::get('gold:vipchannels'));
    }

    public function paySetingUpdate(Request $request)
    {
        $data = $request->all() ?? false;

        Cache::forever('gold:paychannels', $data);
        return $this->success(Cache::get('gold:paychannels'));
    }

    /**
     * 兑换通道更新 function
     *
     * @param Request $request
     * @return void
     */
    public function cashSetingUpdate(Request $request)
    {
        $data = $request->all() ?? false;

        Cache::forever('gold:cashchannels', $data);
        return $this->success(Cache::get('gold:cashchannels'));
    }

    /**
     * 兑换状态处理 function
     *
     * @param Request $request
     * @return void
     */
    public function cashStatus(Request $request)
    {
        //status 1为申请中 2为已通过 3为已到帐 4为已拒绝 5为平台正在打款ing
        $id = $request->id ?? false;
        $operation = $request->operation ?? 'agree'; //refused
        $remark = $request->remark ?? 'remark'; //refused
        $serviceCharge = 2;

        $cashs = Cashs::where('id', $id)->first();
        #serviceCharge 要取自己定义的数值银行卡
        $vipChannel = Cache::get('gold:cashchannels');
        foreach ($vipChannel as $k => $v) {
            if ($v['pay_account_kind'] == 2) {
                $serviceCharge = $v['rate'];
            }
        }
        $sign = [
            'id'   => $id,
            'agent_id'   => $cashs->agent_id,
            'account'   => $cashs->account,
        ];
        #在agree 生成签名  refused kefu  与 third 都需要验证签名处理
        switch ($operation) {
            case 'agree':
                $data =  [
                    'id'    => $id,
                    'agent_id'  => $cashs->agent_id ?? 0,
                    'serviceCharge' => $serviceCharge  .'%',
                    'cash_money' => $cashs->cash_money,
                    'deal_money'   =>  floor($cashs->cash_money * (1 - $serviceCharge / 100))  ,
                    'exchangeType'  => $cashs->exchangeType == 1 ? '支付宝': '银联',
                    'account'   => $cashs->account,
                    'realname'  => $cashs->realname,
                    'remark'    => $cashs->remark,
                    'status'    => $cashs->status,
                    'pay_type'  => $cashs->order_id ? '自动代付':'客服代付',
                    'sign' =>   str_encrypt_sign($sign),
                ];
                #最近一次赠送金额 | 流水  最近一次充值金额 | 充值后新增流水
                #当前玩家最近一次赠送时间到兑换发起时间所有流水的总和
                $apiCoins = ApiConis::where('uid', $cashs->agent_id)
                                        ->orderBy('id', 'DESC')
                                        ->first();
                $data['lastPresent'] = 0;
                $data['presentBankWarter'] = 0;

                if (!empty($apiCoins)) {
                    $data['lastPresent'] = $apiCoins->coins ;
                    $recordDetail = RecordDetail::whereBetween('time', [$apiCoins->created_at,$cashs->created_at])
                                    ->where('uid', $cashs->agent_id)
                                    ->select('yingli')
                                    ->get();
                    #yingli abs
                    $yingliAbsCoins = 0;
                    foreach ($recordDetail as $k => $v) {
                        $yingliAbsCoins += abs($v->yingli);
                    }
                    $data['presentBankWarter'] = floor($yingliAbsCoins)  ;
                }
                # 赠送时间 --兑换时间  得 - 押
                $order = Orders::where('agent_id', $cashs->agent_id)
                                         ->orderBy('id', 'DESC')
                                         ->first();
                $data['lastCharge'] = 0;
                $data['chargeBankWarter'] = 0;
                if (!empty($order)) {
                    $data['lastCharge'] = $order->price  ;

                    $recordDetailOrder = RecordDetail::whereBetween('time', [$order->created_at,$cashs->created_at])
                    ->where('uid', $order->agent_id)
                    ->select('yingli')
                    ->get();
                    #yingli abs
                    $yingliAbsOrder = 0;
                    foreach ($recordDetailOrder as $k => $v) {
                        $yingliAbsOrder += abs($v->yingli);
                    }
                    $data['chargeBankWarter'] = floor($yingliAbsOrder)  ;
                }
                return $this->success($data);

                break;
            case 'refused':
                if ($cashs->status == 3 || $cashs->status == 4 || $cashs->status == 5 || $cashs->status == 6) {
                    return false;
                }
                $result = $this->api->withdrawal_record($cashs->exchangeid, 2);
                if ($result != 'failed') {

                    #status 3为拒绝状态 不能变换状态。 锁死
                    $cashs->status =4;
                    $cashs->manager_id = $this->userId();
                    $cashs->remark = $remark;
                    $cashs->save();
                }

                return $this->success('success');

                break;
            #客服操作，直接设置完成。 不用处理与第三方对接的情况 status 2
            case 'kefu':
                if ($cashs->status == 3 || $cashs->status == 4 || $cashs->status == 5 || $cashs->status == 6) {
                    return false;
                }
                $result = $this->api->withdrawal_record($cashs->exchangeid, 1);
                if ($result != 'failed') {
                    $cashs->status =3;
                    $cashs->remark = $remark;
                    $cashs->manager_id = $this->userId();
                    $cashs->save();
                    return $this->success('success');
                }
                return $this->failed('同步服务器失败,请稍后在试!');
                break;
            case 'third':
                if ($cashs->exchangeType == 1) {
                    return $this->failed('自动代付仅支持银联支付,尚未支持支付宝操作,支付宝只能客服支付.');
                }
                /*
                    0.接入写的HT商户平台,使用SDK处理.
                    1.检测对应的商户ID在平台上的钱数,如果不存在的情况直接跳出,非法操作检测机制
                    2.检测比率的操作 接入的zhong扣2.5元 那是提取后扣的 我这边需要先扣3元的费用. 然后算出真实应付的费用 + 上对应平台的费用 2.5元

                    3.status 5 自动代付中,到帐时间1-24小时 , 6 自动代付失败,已回退金额
                */
                $deal_moeny = floor($cashs->cash_money * (1 - $serviceCharge / 100))  ;  //ubey的手续费是单独扣的处理. 不是在金钱里面

                $balance = $this->checkShopMoney('json');
                if ($balance['stateData']['balance'] <= $deal_moeny) {
                    return $this->failed('平台余额不足');
                }
                $orderNo = 'PAYBANK'.time().date('dHis').mt_rand(100, 200);
                $data = [
                    'trans_money'	=> $deal_moeny,  //银行钱数为分,已经在模型转换
                    'third'	=> 'ubey', //HT平台所支付的渠道,请登录商户后台查看
                    'to_acc_name'	=> $cashs->realname,
                    'to_acc_no'	=> $cashs->account,
                    'notifyurl'	=> Config::get('services.payment.taizi.notiyurl'), //可在后台配置,也可写在参数中
                    'order_id'	=> $orderNo,
                    'app_id'	=> Config::get('services.payment.taizi.app_id'),
                ];
                $data['sign'] = $this->paymentSign($data);
                $rsaData = $this->paymentRsa($data);
                /**
                 * 并发处理的情况 悲观锁，与乐观锁  redis( setnx 是线程模型没有并发问题)
                 *
                 * 应该在PAYSDK重写这里处理. 支付平台服务器处理，本地前端只要做点击限制或者延迟就行.
                 *
                 * 1.区分台子单的打款处理　天天棋牌是单一，其它统一．
                 */
                $http = Http::postArrayByUrl(Config::get('services.payment.taizi.payment'), $rsaData);

                if ($http['code'] == 200) {
                    $content = $this->paymentRsa($http['content'], 'decrypt');
                    $content = json_decode($content, true);
                    #远程平台兑换的情况,可以金钱不够==
                    if (!isset($content['stateCode'])) {
                        return $this->failed('远程服务调用失败,请稍后在试!');
                    }
                    if ($content['stateCode'] != 200) {
                        return $this->failed('远程服务调用异常,请稍后在试');
                    }
                    Log::info('cashs===>', ['http'=>$http,'data'=> $data,'content'=>$content]);
                    #同步状态为5 打款中的状态处理
                    $this->api->withdrawal_record($cashs->exchangeid, 5);

                    $cashs->status =5;
                    $cashs->remark = $remark;
                    $cashs->manager_id = $this->userId();
                    $cashs->order_id = $orderNo;
                    $cashs->save();
                    return $this->success('success');
                }

                Log::info('cashs===>', ['http'=>$http,'data'=> $data]);
                return $this->failed('远程服务调用异常,请稍后在试');

                break;
            default:
                return $this->failed('failed_default');
                break;
        }
    }
    /**
     * 检测商户钱数如不够的情况下,是不能自动代付的操作 function
     *
     * @return void
     */
    public function checkShopMoney($method ='array')
    {
        $data = [
            'app_id'    => Config::get('services.payment.taizi.app_id'),
        ];
        $data['sign'] = $this->paymentSign($data);
        $rsaData = $this->paymentRsa($data);

        $http = Http::postArrayByUrl(Config::get('services.payment.taizi.payBalance'), $rsaData);

        Log::info('checkShopMoneyPost', ['appid' => Config::get('services.payment.taizi.app_id')]);


        if ($http['code'] == 200) {
            $content = $this->paymentRsa($http['content'], 'decrypt');
            $content = json_decode($content, true);

            Log::info('checkShopMoneyPost', ['appid' => Config::get('services.payment.taizi.app_id'),'content' => $content]);

            #远程平台兑换的情况,可以金钱不够==
            if ($content['stateCode'] != 200) {
                return $this->failed('查询平台失败,请联系支付平台对应人员');
            }

            $content['stateData']['balance'] = $content['stateData']['balance'] / 100;

            return $method == 'array'? $this->success($content): $content;
        }
        return $this->failed('查询平台失败,请联系支付平台对应人员.');
    }



    /**
     * 活动赠送的开起与金额设置 function
     *
     * @return void
     */
    public function activeGiveUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'give_start' => 'required|integer',
            'give_money' => 'required|max:100',
            ]);

        if ($validator->fails()) {
            return $this->failed('参数验证失败');
        }
        $giveConfig = [
            'give_start'    => $request->give_start ?? 0,
            'give_money'    => $request->give_money ?? 0,
        ];
        Cache::forever('gold:giveConfig', $giveConfig);

        return $this->success(Cache::get('gold:giveConfig'));
    }
    /**
     * 活动赠送的开起与金额设置 function
     *
     * @return void
     */
    public function activeGive()
    {
        $giveConfig = [
            'give_start'    => 0,
            'give_money'    => 0,
        ];
        $platform = [
            'platform_amount'   => $this->userAmount()
        ];
        if (!Cache::has('gold:giveConfig')) {
            Cache::forever('gold:giveConfig', $giveConfig);
        }
        $give = Cache::get('gold:giveConfig');
        $give['give_start'] =  $give['give_start'] ;
        $give['give_money'] =  $give['give_money'] ;
        
        $platform = array_merge($give, $platform);
        return $this->success($platform);
    }

    /**
     * 上传图片--异步操作直接返回成功 function
     *
     * @param Request $request
     * @param ImageUploadHandler $uploader
     * @param Images $image
     * @return void
     */
    public function images(Request $request, ImageUploadHandler $uploader, Images $image)
    {
        $size = $request->type == 'avatar' ? 362 : 1024;
        $result = $uploader->save($request->image, str_plural($request->type), $this->userId(), $size);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $this->userId();
        $image->save();

        return $this->success($image);
    }
    #后续功能添加-----------------------
    public function WindControl(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $realname = $request->realname ?? null;
        $account = $request->account ?? null;
        $ip = $request->ip ?? null;

        $usersCashs = DB::table('users')
            ->join('qt_cashs', 'users.email', '=', 'qt_cashs.agent_id')
            ->select('users.created_at', 'users.email', 'users.mobile', 'users.bind_id', 'users.status', 'users.ip', 'qt_cashs.realname', 'qt_cashs.account', 'qt_cashs.created_at as cashCreated_at')
            ->when($realname, function ($query) use ($realname) {
                return $query->where('realname', 'like', '%'.$realname.'%');
            })
            ->when($account, function ($query) use ($account) {
                return $query->where('qt_cashs.account', 'like', '%'.$account.'%');
            })
            ->when($ip, function ($query) use ($ip) {
                return $query->where('ip', 'like', '%'.$ip.'%');
            })
            ->orderBy('cashCreated_at', 'DESC')
            ->groupBy('email');
        return new RecorStatisEvery($usersCashs->paginate($paginate));
    }
}
