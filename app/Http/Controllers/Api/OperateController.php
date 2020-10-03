<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Game\RecordDetailStatis;

#分页与单个模型的关系(item)
use App\Http\Resources\RecorStatisdResource;
use App\Http\Resources\RecorStatisd;
use App\Http\Resources\Admin\PayCollection;

use App\Models\Game\Cashs;
use App\Models\Game\Orders;
use App\Models\Server\ApiConis;
use App\Models\Game\DailyBills as daily;

use DB;
use Config;
use Log;

class OperateController extends BaseController
{
    /**
     * 游戏分析--运营数据  (字段的问题,不应使用大写 统一小写与下划线标识 方便框架处理 前端处理了)
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $kindid = $request->kindid ?? 10000;
        $time = $request->time ?? false;
        $user_id = $request->user_id ?? '';
        #搜索直属与非直属操作
        if ($user_id && $kindid != 10000) {
            #根据条件的总计,  总押，总得  总抽水 总盈利. 默认返回所有
            $totalRecordDetail = RecordDetailStatis::select(
                DB::raw('SUM(yazhuTotal) as yazhuTotal'),
                DB::raw('SUM(defenTotal) as defenTotal'),
                DB::raw('SUM(choushuiTotal) as choushuiTotal'),
                DB::raw('SUM(yingliTotal) as yingliTotal'),
                'kindid',
                'tid',
                'created_at',
                'uid',
                'id',
                'time'
            )
            
            ->where('kindid', $kindid);
            $RecordDetailStatis = RecordDetailStatis::select(
                DB::raw('SUM(yazhuTotal) as yazhuTotal'),
                DB::raw('SUM(defenTotal) as defenTotal'),
                DB::raw('SUM(choushuiTotal) as choushuiTotal'),
                DB::raw('SUM(yingliTotal) as yingliTotal'),
                'kindid',
                'tid',
                'created_at',
                'uid',
                'id',
                'time'
            )
            
            ->where('kindid', $kindid)
            ->groupBy('time');

            $RecordDetailStatis = member_search($RecordDetailStatis, $user_id, 'uid');
            $totalRecordDetail = member_search($totalRecordDetail, $user_id, 'uid');
        } else {
            
            #根据条件的总计,  总押，总得  总抽水 总盈利. 默认返回所有
            $totalRecordDetail = RecordDetailStatis::select(
                DB::raw('SUM(yazhuTotal) as yazhuTotal'),
                DB::raw('SUM(defenTotal) as defenTotal'),
                DB::raw('SUM(choushuiTotal) as choushuiTotal'),
                DB::raw('SUM(yingliTotal) as yingliTotal'),
                'kindid',
                'tid',
                'created_at',
                'uid',
                'id'
                )
                ->where('kindid', $kindid)
                ->whereNull('uid');
            $RecordDetailStatis = RecordDetailStatis::where('kindid', $kindid)
                ->whereNull('uid');

        }
        if ($time) {
            $time = explode(' - ', $time);
            $RecordDetailStatis = $RecordDetailStatis->whereBetween('time', [$time[0],$time[1]]);
            $totalRecordDetail = $totalRecordDetail->whereBetween('time', [$time[0],$time[1]]);
        }
        $RecordDetailStatis = $RecordDetailStatis->orderBy('id', 'DESC')->paginate($paginate);

        $totalRecordDetail = $totalRecordDetail->get();

        $data = (new RecorStatisd($RecordDetailStatis))
                ->additional(['meta' => [
                    'total_detail' => $totalRecordDetail,
                    ]]);
        return $data;
    }


    /**
     * 充值记录,涉及上级信息显示与其它 function keyword 全字段搜索
     *
     *  statis 总数是根据时间来区分。 其它条件没有
     * @param Request $request
     * @return void
     */
    public function payAnalysis(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $pay = $request->pay ?? false;
        $user_id = $request->user_id ?? false;
        $keyword = $request->keyword ?? false;
        $orders =  Orders::with('user', 'manager');

        $ordersStatisOffline = Orders::orderBy('id', 'DESC')->where('pay_type', '线下充值');
        $ordersStatisonline = Orders::orderBy('id', 'DESC')->where('pay_type', '!=', '线下充值');
        if ($keyword) {
            $orders = key_word_search($orders, ['order_id','agent_id','pay_type','remark'], $keyword);
        }
        if ($pay == 'offline') {
            $orders = $orders->where('pay_type', '线下充值');
        } elseif ($pay == 'online') {
            $orders = $orders->where('pay_type', '!=', '线下充值');
        }
        if ($time) {
            $time = explode(' - ', $time);
            $orders = $orders->whereBetween('created_at', [$time[0],$time[1]]);
            $ordersStatisOffline = $ordersStatisOffline->whereBetween('created_at', [$time[0],$time[1]]);
            $ordersStatisonline = $ordersStatisonline->whereBetween('created_at', [$time[0],$time[1]]);
        }
        if ($user_id) {
            $orders = member_search($orders, $user_id);
        }

        #换成collection 在单独的sum()计算
        $ordersStatisOffline = $ordersStatisOffline->sum('price');
        $ordersStatisonline = $ordersStatisonline->sum('price');

        $data = [
            'offline'   => $ordersStatisOffline / 100,
            'online'    => $ordersStatisonline / 100
        ];

        return  (new PayCollection($orders->orderBy('id', 'DESC')->paginate($paginate)))
            ->additional(['meta' => [
                'total_detail' => $data,
                ]]);
        ;
    }
    

    /**
     * 兑换记录,显示其它信息 如累计提现次数 总金额 上级信息==操作. 商户号 function
     *  1.1 默认为1为申请中,2为已通过,3为已到帐，4为已拒绝
     *      1.2 累计次数 与累计提现金额计算  status 类型 搜索 与充值少了会员类型
     * @param Request $request
     * @return void
     */
    public function cashAnalysis(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $user_id = $request->user_id ?? false;
        $status = $request->status ?? false;
        $keyword = $request->keyword ?? false;

        $Cashs =  Cashs::with(['manager','paraent']);
        $CashsStatis =  Cashs::orderBy('id', 'DESC');

        #关键字搜索---> 模型，搜索字段 ，返回模型搜索
        if ($keyword) {
            $Cashs = key_word_search($Cashs, ['order_id','agent_id','agent_nickname','realname','account','remark'], $keyword);
        }

        if ($time) {
            $time = explode(' - ', $time);
            $Cashs = $Cashs->whereBetween('created_at', [$time[0],$time[1]]);

            $CashsStatis = $CashsStatis->whereBetween('created_at', [$time[0],$time[1]]);
        }
        if ($status) {
            $Cashs = $Cashs->where('status', $status);
        }

        if ($user_id) {
            $Cashs = member_search($Cashs, $user_id);
        }

        $CashsStatis = $CashsStatis->get();

        $data = [
            '1' => $CashsStatis->where('status', '1')->sum('cash_money'),
            '2' => $CashsStatis->where('status', '2')->sum('cash_money'),
            '3' => $CashsStatis->where('status', '3')->sum('cash_money'),
            '4' => $CashsStatis->where('status', '4')->sum('cash_money'),
        ];

        $Cashs = $Cashs->orderBy('id', 'DESC')->paginate($paginate);

        return (new PayCollection($Cashs))
                    ->additional(['meta' => [
                        'total_detail' => $data,
                        ]]);
        ;
    }

    /**
     * 赠送记录，赠送的数值要*10000 才等于对应的金钱 function
     *
     * @param Request $request
     * @return void
     */
    public function giveAnalysis(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $user_id = $request->user_id ?? false;
        $keyword = $request->keyword ?? false;
        $ApiConis =  ApiConis::with(['manager','user']);

        $ApiConisStatis = ApiConis::where('type','!=',21)
                                ->orderBy('id', 'DESC');
        if ($keyword) {
            $ApiConis = key_word_search($ApiConis, ['uid','remark'], $keyword);
        }
        if ($time) {
            $time = explode(' - ', $time);
            $ApiConis = $ApiConis->whereBetween('created_at', [$time[0],$time[1]]);
            $ApiConisStatis = $ApiConisStatis->whereBetween('created_at', [$time[0],$time[1]]);
        }
        if ($user_id) {
            $ApiConis = member_search($ApiConis, $user_id, 'uid');
        }
        $ApiConis = $ApiConis->orderBy('id', 'DESC')
                                ->where('type','!=',21)
                                ->paginate($paginate);
        #uid 大于6位则是用户ID ，小于6为管理ID

        $ApiConisStatis = $ApiConisStatis->sum('coins') / 10000;
        $data = [
            'total'   => $ApiConisStatis,
        ];
        return (new PayCollection($ApiConis))
                    ->additional(['meta' => [
                        'total_detail' => $data,
                        ]]);
        ;
    }
    /**
     * 每日账单--增加单个用户搜索 function
     *
     * @param Request $request
     * @return void
     */
    public function dailyBillsAnalysis(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $user_id = $request->user_id ?? false;

        if ($user_id) {
            $daily = daily::orderBy('id', 'DESC');
            $dailyStatis = daily::orderBy('id', 'DESC');

            $daily = member_search($daily, $user_id);
            $dailyStatis = member_search($dailyStatis, $user_id);

            $daily =  $daily->select(
                DB::raw('SUM(off_line) as off_line'),
                DB::raw('SUM(online) as online'),
                DB::raw('SUM(coins) as coins'),
                DB::raw('SUM(cashs) as cashs'),
                DB::raw('SUM(account) as account'),
                DB::raw('SUM(bank) as bank'),
                'agent_id',
                'type',
                'time',
                'created_at')
                ->where('type', 'sigle')
                ->groupBy('time');
            $dailyStatis = $daily->where('type', 'sigle');

        } else {
            $daily =  daily::select(
                DB::raw('SUM(off_line) as off_line'),
                DB::raw('SUM(online) as online'),
                DB::raw('SUM(coins) as coins'),
                DB::raw('SUM(cashs) as cashs'),
                DB::raw('SUM(account) as account'),
                DB::raw('SUM(bank) as bank'),
                'agent_id',
                'type',
                'time',
                'created_at')
                ->where('type', 'total')
                ->orderBy('id', 'DESC')
                ->groupBy('time');
            $dailyStatis = daily::where('type', 'total')
                ->orderBy('id', 'DESC');
        }
        if ($time) {
            $time = explode(' - ', $time);
            $daily = $daily->whereBetween('time', [$time[0],$time[1]]);
            $dailyStatis = $dailyStatis->whereBetween('time', [$time[0],$time[1]]);
        }
        $dailyStatis = $dailyStatis->get();
        $data = [
            'off_line'   => $dailyStatis->sum('off_line'),
            'online'   => $dailyStatis->sum('online'),
            'coins'   => $dailyStatis->sum('coins'),
            'cashs'   => $dailyStatis->sum('cashs'),
        ];
        $daily = $daily->paginate($paginate);
        return (new PayCollection($daily))
                ->additional(['meta' => [
                    'total_detail' => $data,
                    ]]);
    }

    /**
     *活动记录报表  function
     *
     * @param Request $request
     * @return void
     */
    public function activeGiveData(Request $request)
    {
        $paginate = $request->paginate ?? 12;
        $time = $request->time ?? false;
        $keyword = $request->keyword ?? false;
        $user_id = $request->user_id ?? false;
        
        $apiConis = ApiConis::with(['manager','user']);
        $conisTotal = ApiConis::where('type',10);

        $apiConis->when($time,function($query) use ($time){
            $time = explode(' - ', $time);

            return $query->whereBetween('created_at', [$time[0],$time[1]]);
        })
        ->when($user_id,function($query) use ($user_id){
            return member_search($query,$user_id,'uid');
        })
        ->when($keyword,function($query) use ($keyword){
            return key_word_search($query,['remark','ip','uid'],$keyword);
        })
        ->where('type',10)
        ->orderBy('id','DESC');
        
        $conisTotal = $conisTotal->sum('coins') / 10000;

        return (new PayCollection($apiConis->paginate($paginate)))
                ->additional(['meta' => [
                    'total_detail' => ['coinsTotal' => $conisTotal],
                    ]]);
        ;
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
