<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\User;
use App\UserSimple;
use App\UserComplex;

use App\Http\Resources\Admin\User as UserCollection;
use App\Models\Game\RecordDetail;
use App\Models\Game\RecordSingle;
use App\Models\Game\RecordDetailStatis;

use App\Models\Game\Cashs;
use App\Models\Game\Orders;
use App\Models\Server\ApiConis;

use App\Models\Game\AgentReport;
use App\Models\Game\Settings;
use DB,Config;
use Cache;

/**
 * 人员管理--用户信息(游戏记录) 与代理信息  class
 */
class PersonnelController extends BaseController
{
    public function user(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $keyword = $request->keyword ?? false;
        $user_id = $request->user_id ?? false;
        $bind = $request->bind ?? false;

        $user = User::select('created_at', 'mobile', 'agent_nickname', 'agent_id', 'bind_id', 'status', 'remark','ip','balance','email');

        if ($keyword) {
            $user = key_word_search($user, ['mobile','agent_id'], $keyword);
        }
        if ($time) {
            $time = explode(' - ', $time);
            $user = $user->whereBetween('created_at', [$time[0],$time[1]]);
        }
        if ($user_id) {
            $user = member_search($user, $user_id);
        }
        if ($bind) {
            switch ($bind) {
                case 'binding':
                    $user = $user->whereNotNull('mobile');
                    break;
                case 'unBinding':
                    $user = $user->whereNull('mobile');
                    break;                
                default:
                    break;
            }
        }
        $user = $user->where('manager', 0)->orderBy('balance', 'DESC');

        return new UserCollection($user->paginate($paginate));
    }
    /**
     * 游戏记录（详细查看带有详细的查询) function
     *
     * @return void
     */
    public function userGameRecord(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;
        $time = $request->time ?? false;
        $kindid = $request->kindid ?? false;


        #总充值 ，总兑换，历史总押，历史总得，历史总盈
        if ($user_id) {
            if ($time) {
                $time = explode(' - ', $time);


                $historychoushuiTotal = RecordSingle::where('uid', $user_id)
                    ->whereBetween('created_at', [$time[0],$time[1]])
                    ->where('type', 'singleUid')
                    ->sum('choushuiTotal') / 10000;
                $historybankBalanceTotal = RecordSingle::where('uid', $user_id)
                    ->whereBetween('created_at', [$time[0],$time[1]])
                    ->where('type', 'singleUid')
                    ->sum('bankBalance') / 10000;

                $historyyazhuTotal = RecordSingle::where('uid', $user_id)
                    ->whereBetween('created_at', [$time[0],$time[1]])
                    ->where('type', 'singleUid')
                    ->sum('yazhuTotal') / 10000;
                $historydefenTotal = RecordSingle::where('uid', $user_id)
                    ->whereBetween('created_at', [$time[0],$time[1]])
                    ->where('type', 'singleUid')
                    ->sum('defenTotal') / 10000;
                $historyyingliTotal = RecordSingle::where('uid', $user_id)
                    ->whereBetween('created_at', [$time[0],$time[1]])
                    ->where('type', 'singleUid')
                    ->sum('yingliTotal') / 10000;
            } else {
                $historychoushuiTotal = RecordSingle::where('uid', $user_id)
                    ->where('type', 'singleUid')
                    ->sum('choushuiTotal') / 10000;
                $historybankBalanceTotal = RecordSingle::where('uid', $user_id)
                    ->where('type', 'singleUid')
                    ->sum('bankBalance') / 10000;
                $historyyazhuTotal = RecordSingle::where('uid', $user_id)
                    ->where('type', 'singleUid')
                    ->sum('yazhuTotal') / 10000;
                $historydefenTotal = RecordSingle::where('uid', $user_id)
                    ->where('type', 'singleUid')
                    ->sum('defenTotal') / 10000;
                $historyyingliTotal = RecordSingle::where('uid', $user_id)
                    ->where('type', 'singleUid')
                    ->sum('yingliTotal') / 10000;
            }
            $data = [
                'historychoushuiTotal'  => $historychoushuiTotal,
                'historybankBalanceTotal'   => $historybankBalanceTotal,
                'historyYazhuTotal'=>$historyyazhuTotal,
                'historyDefenTotal'=>$historydefenTotal,
                'historyYingliTotal'=> $historyyingliTotal
            ];
            if ($kindid && $kindid != 10000) {
                $record = RecordSingle::where('uid', $user_id)
                    ->orderBy('id', 'DESC')
                    ->where('type', '!=', 'singleUid')
                    ->where('kindid', $kindid);
            } else {
                $record = RecordSingle::where('uid', $user_id)
                    ->orderBy('id', 'DESC')
                    ->where('type', 'singleUid');
            }
            return (new UserCollection($record->paginate($paginate)))
                        ->additional(['meta' => [
                            'total_data' => $data,
                            ]]);
            ;
        }
        return $this->notFond();
    }
    /**
     * 游戏记录10分里面的详细记录,所有 function
     *
     * @return void
     */
    public function userGameRecordTime(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;
        $time = $request->time ?? false;

        if ($user_id && $time) {
            $time = explode(' - ', $time);

            $record = RecordDetail::where('uid', $user_id)->whereBetween('time', [$time[0],$time[1]])->get();

            return $this->success($record);
        }
        return $this->notFond();
    }
    /**
     * 游戏单个的提现记录 function
     *
     * @return void
     */
    public function gameCashs(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;
        $time = $request->time ?? false;

        $cashs = Cashs::with(['manager','paraent'])
                    ->when($user_id, function ($query) use ($user_id) {
                        return $query->where('agent_id', $user_id);
                    })
                    ->when($time, function ($query) use ($time) {
                        $time = explode(' - ', $time);
                        return $query->whereBetween('created_at', [$time[0],$time[1]]);
                    });
        $cashsTotal = Cashs::when($user_id, function ($query) use ($user_id) {
                            return $query->where('agent_id', $user_id);
                        })
                        ->when($time, function ($query) use ($time) {
                            $time = explode(' - ', $time);
                            return $query->whereBetween('created_at', [$time[0],$time[1]]);
                        })
                        ->whereIn('status',[2,3,5])
                        ->sum('cash_money') / 100;
        return (new UserCollection($cashs->orderBy('id','DESC')->paginate($paginate)))
                        ->additional(['meta' => [
                            'total_data' => $cashsTotal,
                            ]]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function gameOrders(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;
        $time = $request->time ?? false;
        if ($user_id) {
            if ($time) {
                $time = explode(' - ', $time);
                $orders = Orders::where('agent_id', $user_id)
                                ->whereBetween('created_at', [$time[0],$time[1]])
                                ->orderBy('id','DESC')
                                ->with(['manager']);
                $ordersTotal = Orders::where('agent_id', $user_id)
                                ->whereBetween('created_at', [$time[0],$time[1]])
                                ->sum('price') / 100;
            } else {
                $orders = Orders::where('agent_id', $user_id)
                            ->orderBy('id','DESC')
                            ->with(['manager']);
                $ordersTotal = Orders::where('agent_id', $user_id)->sum('price') / 100;
            }
    
            return  (new UserCollection($orders->paginate($paginate)))
                        ->additional(['meta' => [
                            'total_data' => $ordersTotal,
                            ]]);
        }
        return $this->notFond();
    }
    /**
     * 游戏充值--TYPE为7 function
     *
     * @return void
     */
    public function gamePay(Request $request)
    {
        $user_id = $request->user_id ?? false;
        $pay_number = $request->pay_number ?? false;

        $remark = $request->remark ?? '尚未备注';
        $order_id = 'BOSS'.date('YmdHis').time().mt_rand(100000, 900000);
        #线下的price 应该是
        if ($user_id) {
            $addCoins  = $this->api->addCoins($user_id, $pay_number * 10000, 7);
            //$pay_number * 10000
            if (isset($addCoins['coins'])) {
                $data =[
                    'agent_id'   => $user_id,
                    'manager_id'    => \Auth::guard('api')->id(),
                    'price' => $pay_number * 100,
                    'remark'    => $remark,
                    'before_coins'  => $addCoins['coins'] + $addCoins['bank']  - $pay_number * 10000,
                    'coins'   => $addCoins['coins'] ?? 0,
                    'bank'  => $addCoins['bank'] ?? 0,
                    'pay_type'  => '线下充值',
                    'order_id'  => $order_id,
                ];

                Orders::create($data);
                return $this->success('success');
            }
            return $this->internalError('远程调用充值失败,请稍后重试!');
        }
    }

    /**
     * 游戏余额，应该判断当前帐号的钱数--TYPE为7 function
     *
     * @return void
     */
    public function gamePayGive(Request $request)
    {
        $user_id = $request->user_id ?? false;
        $pay_number = $request->pay_number ?? false;
        $remark = $request->remark ?? '尚未备注';
        #判断当前用户的额度， 1为平台的额度。 缓存设置 platformAmount
        if ($this->userInfoBase()->amount <= $pay_number
            || $this->userInfoBase()->amount == 0) {
            return $this->failed('帐号平台余额不足或者不能直接赠送全部');
        }
        if ($user_id) {
            $addCoins  = $this->api->addCoins($user_id, $pay_number * 10000, 6);
            if (isset($addCoins['coins'])) {
                $data =[
                    'uid'   => $user_id,
                    'manager_id'    => $this->userId(),
                    'before_coins'  => $addCoins['coins'] + $addCoins['bank']  - $pay_number * 10000,
                    'coins'   => $pay_number * 10000,
                    'type'  => 6,
                    'remark'    => $remark,
                    'balance'   => $addCoins['bank'] + $addCoins['coins'],
                    'bank'  => $addCoins['bank'],
                ];
                ApiConis::create($data);
                User::where('id', $this->userId())->decrement('amount', $pay_number);
                return $this->success('success');
            }
            return $this->internalError('远程调用充值失败,请稍后重试!');
        }
    }
    /**
     * 金币扣除 默认-1 使用线上巅峰的接口 负数处理 function
     *
     * @param Request $request
     * @return void
     */
    public function goldDeduct(Request $request)
    {
        $user_id = $request->user_id ?? false;
        $pay_number = $request->pay_number ?? false;
        $remark = $request->remark ?? '尚未备注';

        if ($user_id) {
            $pay_number =  $pay_number * 10000 * -1;

            $addCoins  = $this->api->addCoins($user_id,$pay_number, 6);
            if (isset($addCoins['coins'])) {
                $data =[
                    'uid'   => $user_id,
                    'manager_id'    => $this->userId(),
                    'before_coins'  => $addCoins['coins'] + $addCoins['bank']  - $pay_number ,
                    'coins'   => abs( $pay_number )  ,
                    'type'  => 21,
                    'remark'    => $remark,
                    'balance'   => $addCoins['bank'] + $addCoins['coins'],
                    'bank'  => $addCoins['bank'],
                ];
                ApiConis::create($data);
                User::where('id', $this->userId())->decrement('amount', $pay_number);
                return $this->success('success');
            }
            return $this->internalError('远程调用充值失败,请稍后重试!');
        }
    }

    /**
     * 用户 赠送记录表 function
     *
     * @param Request $request
     * @return void
     */
    public function gameGive(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;
        $time = $request->time ?? false;
        if ($user_id) {
            if ($time) {
                $time = explode(' - ', $time);

                $ApiConis = ApiConis::where('uid', $user_id)
                            ->whereBetween('created_at', [$time[0],$time[1]])
                            ->orderBy('id','DESC')
                            ->with('manager');
                $ApiConisTotal = ApiConis::where('uid', $user_id)->whereBetween('created_at', [$time[0],$time[1]])->sum('coins') / 10000;
            } else {
                $ApiConis = ApiConis::where('uid', $user_id)
                            ->orderBy('id','DESC')
                            ->with('manager');
                $ApiConisTotal = ApiConis::where('uid', $user_id)
                            ->sum('coins') / 10000;
            }
            return (new UserCollection($ApiConis->paginate($paginate)))
                    ->additional(['meta' => [
                        'total_data' => $ApiConisTotal,
                        ]]);
        }
        return $this->notFond();
    }

    /**
     * 用户 金币扣除记录 --与总的历史扣除处理function
     *
     * @param Request $request
     * @return void
     */
    public function goldDeductGet(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;
        $time = $request->time ?? false;
        $keyword = $request->keyword ?? false;

        if ($user_id) {
            if ($time) {
                $time = explode(' - ', $time);
                $ApiConis = ApiConis::where('uid', $user_id)
                                ->where('type',21)
                                ->whereBetween('created_at', [$time[0],$time[1]])
                                ->with('manager');
                $ApiConisTotal = ApiConis::where('uid', $user_id)
                                ->where('type',21)
                                ->whereBetween('created_at', [$time[0],$time[1]])
                                ->sum('coins') / 10000;
            } else {
                $ApiConis = ApiConis::where('uid', $user_id)
                                ->where('type',21)
                                ->with('manager');
                $ApiConisTotal = ApiConis::where('uid', $user_id)
                                ->where('type',21)
                                ->sum('coins') / 10000;
            }
            return (new UserCollection($ApiConis->paginate($paginate)))
                    ->additional(['meta' => [
                        'total_data' => $ApiConisTotal,
                        ]]);
        } else {
            $ApiConis = ApiConis::when($time,function($query) use($time){
                $time = explode(' - ', $time);
                return $query->whereBetween('created_at', [$time[0],$time[1]]);

            })
            ->when($user_id,function($query) use($user_id){
                return $query->where('uid',$user_id);
            })
            ->when($keyword,function($query) use($keyword){
                return  key_word_search($query, ['uid'], $keyword);
            })
            ->where('type',21)->with('manager');
            return (new UserCollection($ApiConis->paginate($paginate)));
        }
        return $this->notFond();
    }

    /**
     * 当前游戏帐号的余额 function
     *
     * @param Request $request
     * @return void
     */
    public function getUserinfo(Request $request)
    {
        $user_id = $request->user_id ?? false;
        
        $user = $this->api->getUserInfo($user_id);

        if ($user) {
            #总充值，历史总得，历史总盈
            $orderPay = Orders::where('agent_id', $user_id)->sum('price') / 100;
            $Cashs = Cashs::where('agent_id', $user_id)->sum('cash_money') / 100;
            
            $userInfo = User::where('agent_id', $user_id)->first();

            $RecordDe = RecordDetailStatis::where('uid', $user_id)->sum('defenTotal') / 10000;
            $RecordYa = RecordDetailStatis::where('uid', $user_id)->sum('yazhuTotal') / 10000;
            
            $user['coins'] = $user['coins'] / 10000;
            $user['bank'] = $user['bank'] / 10000;
            $data = [
                'payTotal'  => $orderPay ?? 0,
                'cashTotal'  => $Cashs ?? 0,
                'historyDe' => $RecordDe ?? 0,
                'historyYa' => $RecordYa ?? 0,
                'historyYl' => round($RecordYa - $RecordDe, 2) ,

                #总充值 总押，总得 备注信息 上级 手机 最近 登录
                'remark'    => $userInfo->remark ?? '',
                'bind_id'   => $userInfo->bind_id ?? '',
                'mobile'    => $userInfo->mobile ?? '',
            ];

            return $this->success(array_merge($user, $data));
        }
        return $this->notFond();
    }
    /**
     * 用户信息修改 可以单个手机号 与上级 密码 备注 处理  与单独的封号操作 function
     *
     * @return void
     */
    public function userInfoEdit(Request $request)
    {
        $mobile = $request->mobile ?? false;
        $parent_id = $request->parent_id ?? false;
        $password = $request->password ?? false;
        $remark = $request->remark ?? false;
        $user_id = $request->user_id ?? false;
        
        //修改本数据 与游戏数据库
        if ($mobile && $user_id) {
            User::where('agent_id', $user_id)->update(['mobile'=>$mobile]);
            $phone = [
                'phone' => $mobile
            ];
            $result = $this->api->modifyUserInfo($user_id, $phone, 'phone');
            if ($result != 'failed') {
                return $this->success('success');
            }
            return  $this->internalError('远程服务调用失败,请稍后重试!');

        }

        if ($parent_id && $user_id) {
            User::where('agent_id', $user_id)->update(['bind_id'=>$parent_id]);
            return $this->success('success');
        }
        //外部修改密码，这里是游戏里面的
        if ($password && $user_id) {
            $pwd =  [
                'pwd'   => $password,
            ];
            $result = $this->api->modifyUserInfo($user_id, $pwd, 'password');
            if ($result != 'failed') {
                return $this->success('success');
            }
            return  $this->internalError('远程服务调用失败,请稍后重试!');
        }

        if ($remark && $user_id) {
            User::where('agent_id', $user_id)->update(['remark'=>$remark]);
            return $this->success('success');
        }
        return $this->failed('failed');
    }
    /**
     * 游戏封号与解封操作 1为封号 0为正常 function
     *
     * @param Request $request
     * @return void
     */
    public function userLock(Request $request)
    {
        $status = $request->status ?? '';
        $user_id = $request->user_id ?? '';
        if ($user_id) {
            switch ($status) {
                case 1:
                    if ($this->api->bindUser($user_id, 'bindUser')) {
                        return $this->success('lock');
                    }
                    return $this->failed('failedLock');
                    break;
                case 0:
                        if ($this->api->bindUser($user_id, 'unBindUser')) {
                            return $this->success('unLock');
                        }
                        return $this->failed('unLock');
                    break;
                default:
                    return $this->failed('defaultFailed');
                    break;
            }
        }
    }
    /**
     * 用户信息--兑换绑定 function
     *
     * @param Request $request
     * @return void
     */
    public function userCashBind(Request $request)
    {
        $user_id = $request->user_id ?? '';
        if ($user_id) {
            return $this->success($this->api->getUserWithdrawalInfo($user_id)) ;
        }
        return $this->failed('failed');
    }

    public function userCashBindUpdate(Request $request)
    {
        $user_id = $request->user_id ?? '';
        $behavior = $request->behavior ?? '';
        $data = $request->except(['user_id','behavior']) ;
        $dataClear =  [];
        
        switch ($behavior) {
            case '1':
                $result =  $this->api->modifyUserInfo($user_id, $data);
                if ($result != 'failed' ) {
                    return $this->success('success');
                }
                return $this->failed('failed');
                break;
            case '2':
                    $result =  $this->api->modifyUserInfo($user_id, $data);
                    if ($result != 'failed' ) {
                        return $this->success('success');
                    }
                    return $this->failed('failed');
                break;
            default:
                # code...
                break;
        }
    }
    /**
     * 充值时时间 与 当前点击时间的 yingli function
     *
     * @param Request $request
     * @return void
     */
    public function chargeWater(Request $request)
    {
        $order_id = $request->order_id ?? false;
        $time = date('Y-m-d H:i:s');

        #订单的创建时间与点击时间当前的流水
        $order = Orders::where('order_id', $order_id)->first();
        $chargeWater = 0;
        if (!empty($order)) {
            $recordDetailOrder = RecordDetail::whereBetween('time', [$order->created_at,$time])
            ->where('uid', $order->agent_id)
            ->select('yingli')
            ->get();
            #yingli abs
            $yingliAbsOrder = 0;
            foreach ($recordDetailOrder as $k => $v) {
                $yingliAbsOrder += abs($v->yingli);
            }
            $chargeWater = floor($yingliAbsOrder);
        }
        return $this->success(['chargeWater' => $chargeWater]);
    }


    /**
     * 充值时时间 与 当前点击时间的 yingli function
     *
     * @param Request $request
     * @return void
     */
    public function giftWater(Request $request)
    {
        $uid = $request->uid ?? false;
        $created_at = $request->created_at ?? false;
        $time = date('Y-m-d H:i:s');

        #订单的创建时间与点击时间当前的流水
        $ApiConis = ApiConis::where('uid', $uid)
                ->where('created_at',$created_at)
                ->first();
        $giftWater = 0;
        if (!empty($ApiConis)) {
            $recordDetailOrder = RecordDetail::whereBetween('time', [$ApiConis->created_at,$time])
            ->where('uid', $ApiConis->uid)
            ->select('yingli')
            ->get();
            #yingli abs
            $yingliAbsOrder = 0;
            foreach ($recordDetailOrder as $k => $v) {
                $yingliAbsOrder += abs($v->yingli);
            }
            $giftWater = floor($yingliAbsOrder);
        }
        return $this->success(['giftWater' => $giftWater]);
    }


    //****************************人员管理 代理信息*****************************************
    /**
     * 人员管理--代理信息 function
     *
     * @return void
     */
    public function agent(Request $request)
    {
        // $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $keyword = $request->keyword ?? false;
        
        $user  = UserComplex::select('id', 'created_at', 'email', 'bind_id', 'manager')
        ->when($time,function($query) use ($time){
            $time = explode(' - ', $time);
            return  $query->whereBetween('created_at', [$time[0],$time[1]]);
        })
        ->when($keyword,function($query) use ($keyword){
            return key_word_search($query,['email','bind_id'],$keyword);
        })
        ->where('manager', 1);
        return (new UserCollection($user->paginate(8)));
    }
    public function agentPay(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;

        $AgentReport = AgentReport::select('directly_pay', 'directly_not_pay', 'time', 'id')->orderBy('id', 'DESC')->where('status', 1);
        if ($user_id) {
            $AgentReport = $AgentReport->where('uid', $user_id);
            return new UserCollection($AgentReport->paginate($paginate));
        }
        return $this->notFond();
    }
    public function agentCashs(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;

        $AgentReport = AgentReport::select('directly_pay', 'directly_not_pay', 'time', 'id')->orderBy('id', 'DESC')->where('status', 2);

        if ($user_id) {
            $AgentReport = $AgentReport->where('uid', $user_id);
            return new UserCollection($AgentReport->paginate($paginate));
        }
        return $this->notFond();
    }


    public function agentGive(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;

        $ApiConis = ApiConis::select('id', 'manager_id', 'coins', 'type', 'remark', 'balance', 'created_at','uid')->orderBy('id', 'DESC');
        if ($user_id) {
            $ApiConis = $ApiConis->where('manager_id', $user_id);

            $ApiConisTotal = $ApiConis->where('manager_id', $user_id)->sum('coins') / 10000;

            return (new UserCollection($ApiConis->paginate($paginate)))
                        ->additional(['meta' => [
                            'total_data' => $ApiConisTotal,
                            ]]);
        }
        return $this->notFond();
    }

    public function agentRecord(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $user_id = $request->user_id ?? false;
        $kindid = $request->kindid ?? false;

        $agentReport = AgentReport::select(
            'id',
            'time',
            'directly_ya',
            'directly_de',
            'directly_yl',
            'directly_not_ya',
            'directly_not_de',
            'directly_not_yl',
            'kindid'
        )
        ->orderBy('id', 'DESC');
        if ($kindid && $kindid != 10000) {
            $agentReport = $agentReport->where('status', 4)->where('kindid', $kindid);

            $agentReportTotal = AgentReport::orderBy('id', 'ASC')
                                    ->where('status', 4)
                                    ->where('kindid', $kindid);
        } else {
            $agentReport = $agentReport->where('status', 3);

            $agentReportTotal = AgentReport::orderBy('id', 'ASC')->where('status', 3);
        }

        
        if ($user_id) {
            $agentReport = $agentReport->where('uid', $user_id);

            $agentReportTotal = $agentReportTotal->select(
                DB::raw('SUM(directly_ya) as directly_ya'),
                DB::raw('SUM(directly_de) as directly_de'),
                DB::raw('SUM(directly_yl) as directly_yl'),
                DB::raw('SUM(directly_not_ya) as directly_not_ya'),
                DB::raw('SUM(directly_not_de) as directly_not_de'),
                DB::raw('SUM(directly_not_yl) as directly_not_yl')
            )
            ->where('uid', $user_id)
            ->get();
            return (new UserCollection($agentReport->paginate($paginate)))
                        ->additional(['meta' => [
                            'total_data' => $agentReportTotal,
                ]]);
        }
        return $this->notFond();
    }
    /**
     * 上级修改---开通时间 function
     *
     * @param Request $request
     * @return void
     */
    public function agentEdit(Request $request)
    {
        $user_id = $request->user_id ?? false;
        $parent_id = $request->parent_id ?? false;

        $user = User::select('bind_id','agent_id')
                        ->where('email', $user_id)
                        ->first();
        $data = [
            'user_id'   => $user->bind_id == 0 ? '不可修改' : $user->bind_id,
            'replace_gameid' => $user->agent_id
        ];

        return $this->success($data);
    }



    /**
     * 上级修改---开通时间 function
     *
     * @param Request $request
     * @return void
     */
    public function agentEditOp(Request $request)
    {
        $user_id = $request->user_id ?? false;
        $replace_id = $request->replace_id ?? false;
        $replace_gameid = $request->replace_gameid ?? false;
        #修改上级ID
        if ($user_id && $replace_id) {
            if ($user_id == 80000) {
                return $this->failed('failed_80000_edit_error');
            }
            if ($user_id == $replace_id){
                return $this->failed('上级ID不能设置为自己,如不绑定他人,设置为0即可.');
            }
            $user = User::where('email', $user_id)->first();
            $replace = User::select('email')->where('email', $replace_id)->value('email');

            if (empty($user) || empty($replace)) {
                return $this->notFond();
            }

            $user->bind_id = (int) $replace;
            $user->save();

            return $this->success('success');
        }
        #绑定游戏ID处理
        if ($user_id && $replace_gameid) {

            $user = User::where('email', $user_id)->first();
            $replace = User::where('email',$replace_gameid)->first();

            if (empty($user) || empty($replace) ) {
                return $this->notFond();
            }

            $user->agent_id = (int) $replace_gameid;
            $user->save();

            $replace->bind_id = $user_id;
            $replace->save();

            return $this->success('success');
        }



    }
    /**
     * 代理的--游戏总余额 (平台剩余额度, 代理剩余额度) function
     *
     * @param Request $request
     * @return void
     */
    public function agentGameTotal(Request $request)
    {
        $user_id = $request->user_id ?? false;

        if(!Cache::has('platformAmount')){
            Cache::forever('platformAmount',1000000);
        }
        if ($user_id) {
            $user = $this->getUserInfoBase($user_id);

            $data = [
                'platformAmount'    =>  (int) Cache::get('platformAmount'),
                'agentAmont'    => $user->amount ?? 0
            ];
            return $this->success($data);
        }
        return $this->notFond();
    }


    /**
     * 代理的--游戏总余额 (平台剩余额度, 代理剩余额度) function
     *
     * @param Request $request
     * @return void
     */
    public function agentGameTotalEdit(Request $request)
    {
        $user_id = $request->user_id ?? false;
        $give = $request->give ?? 0; //支持负数的操作
        $platformAmount = (int) Cache::get('platformAmount');

        if ($platformAmount <= 0 || $give >= $platformAmount) {
            return $this->failed('平台额度已消耗完毕,请联系相应人员进行平台充值,或充值数大于平台额度');
        }
        #只有管理员为1才能修改额度
        if ($this->userInfoBase()->id == 1) { 
            if ($give <= 0){
                User::where('id', $user_id)->decrement('amount', abs($give) );
                Cache::increment('platformAmount', abs($give) );
            } else {
                User::where('id', $user_id)->increment('amount', $give);
                Cache::decrement('platformAmount', $give);   
            }
            return $this->success('success');
        }
        return $this->failed('管理员id为1才能修改额度与增加,其它操作无效');
    }
    /**
     * 游戏总余额,取所有的直属玩家 function
     *
     * @return void
     */
    public function agentTotal(Request $request)
    {
        $user_id = $request->user_id ?? false;

        if ($user_id) {
            $user = UserSimple::select('created_at', 'email', 'bind_id', 'manager')
                                ->where('email', $user_id)
                                ->first();
            $relation = '['. $user->getAgentNumberIdAttribute().']';

            #直属用户的总游戏余额 与 银行余额
            $data = $this->api->getCoins(false, 'limitplayer', $relation);

            if ($data['totalCoins']) {
                $data['totalCoins'] = $data['totalCoins'] / 10000;
            }

            if ($data['totalBanks']) {
                $data['totalBanks'] = $data['totalBanks'] / 10000;
            }
            return $this->success($data);
        }
    }
    /**
     * 本项目的二维码地址生成 function
     *
     * @return void
     */
    public function agentQrcode(Request $request)
    {
        $user_id = $request->user_id ?? false;

        $projectUrl = Config::get('app.url');
        #二维码图片不存在则自动生成处理
        if ($user_id) {

            $file = $projectUrl . '/qrcode/'.$user_id.'.png';

            return $this->success($file);
        }
        return $this->failed('failed');
    }

    /**
     * 邀请码设置开关,统一设置配置表来进行请求 function
     *
     * @return void
     */
    public function inviteCodeSw()
    {
        $settings = Settings::where('key','sm_auto_bind')->first();
        
        return $this->success($settings);
    }

    /**
     * 邀请码设置开关,统一设置配置表来进行请求 function
     *
     * @return void
     */
    public function inviteCodeSwEdit(Request $request)
    {
        $type = $request->type ?? false;
        $sw = $request->sw ?? false;
        if ($type) {
            $settings = Settings::where('key',$type)->update(['value'=> $sw]);
            return $this->success($settings);
        }
        return $this->failed('failed');
    }


    //****************************人员管理 代理信息*****************************************
}
