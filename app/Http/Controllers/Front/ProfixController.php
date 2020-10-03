<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;
use Config;
use Log;

use App\Models\Game\Orders;
use App\Models\Game\Cashs;
use App\User;

use App\Models\Game\DailyBills;
use Illuminate\Support\Carbon;

/**
 * 盈利分成平台计算 class
 */
class ProfixController extends CommonController
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function index()
    {
        #二维码图片  昨日新增，昨日充值数量
        $this->qrcodeGeneric();

        if (Config::get('app.name') == 'tiantian') {
            $sharePng = '/qrcode/'.$this->authIdEmail().'_share.png';
        } elseif(Config::get('app.name') == 'jg'){
            $sharePng = '/qrcode/'.$this->authIdEmail().'_share.png';
        }else {
            $sharePng = '/qrcode/'.$this->authIdEmail().'.png';
        }
        $today = Carbon::parse('today')->format('Y-m-d H:i:00');
        $todayEnd =  Carbon::parse('today')->format('Y-m-d 23:59:59');

        $yesterday = Carbon::parse('yesterday')->format('Y-m-d H:i:00');
        $yesterdayEnd =  Carbon::parse('yesterday')->format('Y-m-d 23:59:59');

        $GetAgentNumberId = $this->GetAgentNumberId();
        #玩家数量--今日充值--今日兑换  
        $playerNumber = $this->getAgentNumberAttribute();
        #等于自己的话，表明为空的代理下级
        if ($playerNumber == session('system_front_id')) {
            $playerNumber = 0;
        }

        $orderSum = Orders::select('created_at','agent_id', 'price')
                ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                ->whereBetween('created_at',[$today,$todayEnd])
                ->sum('price');
        $cashsSum = Cashs::select('created_at','agent_id', 'cash_money')
                ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                ->where('status',3)
                ->whereBetween('created_at',[$today,$todayEnd])
                ->sum('cash_money');
        #玩家余额-昨日充值-昨日兑换
        $orderSumYesterday = Orders::select('created_at','agent_id', 'price')
                ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                ->whereBetween('created_at',[$yesterday,$yesterdayEnd])
                ->sum('price');
        $cashsSumYesterday = Cashs::select('created_at','agent_id', 'cash_money')
                ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                ->where('status',3)
                ->whereBetween('created_at',[$yesterday,$yesterdayEnd])
                ->sum('cash_money');

        #昨日新增--昨日充值数量
        $yesterdayUser = User::where('bind_id',$this->authIdEmail())
                ->whereBetween('created_at',[$yesterday,$yesterdayEnd])
                ->count();
        
        $yesterdayUserPay = Orders::select('created_at','agent_id', 'price')
                ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                ->whereBetween('created_at',[$yesterday,$yesterdayEnd])
                ->count();        



        return view('Front.Profix.index', compact('sharePng','playerNumber','orderSum','cashsSum','orderSumYesterday','cashsSumYesterday','GetAgentNumberId','yesterdayUser','yesterdayUserPay'));
    }
    /**
     * 充值记录 function
     *
     * @return void
     */
    public function recharge(Request $rquest)
    {
        return view('Front.Profix.recharge');
    }

    public function rechargeData()
    {
        $GetAgentNumberId = $this->GetAgentNumberId();
        $orders = Orders::select('created_at', 'agent_id', 'price', 'pay_type')
                            ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                            ->orderBy('id', 'DESC');
        return  DataTables::of($orders)
                    ->make(true);
    }
    /**
     * 兑换记录 function
     *
     * @return void
     */
    public function cash()
    {
        return view('Front.Profix.cash');
    }


    public function cashData()
    {
        $GetAgentNumberId = $this->GetAgentNumberId();
        $orders = Cashs::select('created_at', 'agent_id', 'cash_money')
                            ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                            ->where('status',3)
                            ->orderBy('id', 'DESC');
        return  DataTables::of($orders)
                    ->make(true);
    }
    /**
     * 玩家详细   (账单详细)function
     *
     * @return void
     */
    public function player()
    {
        return view('Front.Profix.player');
    }

    public function playerData()
    {
        $GetAgentNumberId = $this->GetAgentNumberId();
        $DailyBills = DailyBills::select(
                                'time',
                                'agent_id',
                                'type',
                                'off_line',
                                'online',
                                'account',
                                'bank',
                                'cashs'
                            )
                            ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                            ->where('type','sigle')
                            ->orderBy('id', 'DESC');
        return  DataTables::of($DailyBills)
                    ->editColumn('off_line',function($DailyBills){
                        return $DailyBills->off_line + $DailyBills->online;
                    })
                    ->editColumn('account', function($DailyBills){
                        return $DailyBills->account + $DailyBills->bank;
                    })
                    ->make(true);
    }


    /**
     * 账单详情  盈利 = 充值 -兑换 -余额  function
     *
     * @return void
     */
    public function Bill()
    {
        return view('Front.Profix.Bill');
    }

    public function BillData()
    {
        $GetAgentNumberId = $this->GetAgentNumberId();
        $DailyBills = DailyBills::select(
                                'time',
                                DB::raw('SUM(off_line) as off_line'),
                                DB::raw('SUM(online) as online'),
                                DB::raw('SUM(account) as account'),
                                DB::raw('SUM(bank) as bank'),
                                DB::raw('SUM(cashs) as cashs')
                            )
                            ->whereRaw(DB::raw("agent_id IN ($GetAgentNumberId)"))
                            ->where('type','sigle')
                            ->orderBy('id', 'DESC')
                            ->groupBy('time');
        return  DataTables::of($DailyBills)
                    ->editColumn('off_line',function($DailyBills){
                        return $DailyBills->off_line + $DailyBills->online;
                    })
                    ->editColumn('account', function($DailyBills){
                        return $DailyBills->account + $DailyBills->bank;
                    })
                    ->addColumn('profix',function($DailyBills){
                        return  ( $DailyBills->off_line + $DailyBills->online ) - 
                                    $DailyBills->cashs -   ($DailyBills->account + $DailyBills->bank);
                        
                    })
                    ->make(true);
    }
}
