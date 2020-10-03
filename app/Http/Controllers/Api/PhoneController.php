<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\UserSimple;
use App\Models\Game\AgentReport;
use App\Models\Server\ApiConis;
use App\Models\Game\DailyBills;
use DB;


/**
 * 手机端应用的统计与搜索需求 设置 class
 *
 * 1.营收报告-概况  2.玩家管理-概况   3.玩家管理-游戏记录，按游戏筛选   4.代理管理-概况  5.代理管理-游戏记录增加游戏筛选。
 */
class PhoneController extends BaseController
{
    /**
     * 首页统计 function
     *
     * @return void
     */
    public function index(Request $request)
    {
        //根据时间文字提取对应的区间显示,与行为操作 orderCash  totalYDl
        //玩家统计  充值/兑换  总押/总得/总盈利  营收报表 每天的统计统计
        $orderCash = $request->orderCash ?? 'orderCash';
        $totalYl = $request->totalYl ?? 'totalYl';
        $time = $request->time ?? '今日';

        $id = \Auth::guard('api')->id();
        $user =  UserSimple::where('id', $id)->first();

        $data['statis']['playerStatis'] =[
            'getAgentNumber'    =>
                $user->getAgentNumberAttribute(),
            'getPlayerNumber'    =>
                $user->getPlayerNumberAttribute(),
            'getAgentDirectlyNumber'    =>
                $user->getAgentDirectlyNumberAttribute(),
        ];

        if ($orderCash == 'orderCash' && $time) {
            $time_str = time_str($time);
            $data['statis']['orderCash']    = [
                'getDirectlyOrderAttribute'    =>
                    $user->getDirectlyOrderAttribute($time_str),
                'getDirectlyNotPlayerOrderAttribute'    =>
                    $user->getDirectlyNotPlayerOrderAttribute($time_str),
                'getDirectlyCashsAttribute'    =>
                    $user->getDirectlyCashsAttribute($time_str),
                'getDirectlyNotPlayerCashsAttribute'    =>
                    $user->getDirectlyNotPlayerCashsAttribute($time_str),
            ];
        } else {
            $data['statis']['orderCash']    = [
                'getDirectlyOrderAttribute'    =>
                    $user->getDirectlyOrderAttribute(),
                'getDirectlyNotPlayerOrderAttribute'    =>
                    $user->getDirectlyNotPlayerOrderAttribute(),
                'getDirectlyCashsAttribute'    =>
                    $user->getDirectlyCashsAttribute(),
                'getDirectlyNotPlayerCashsAttribute'    =>
                    $user->getDirectlyNotPlayerCashsAttribute(),
            ];
        }
        if ($totalYl == 'totalYl' && $time) {
            $time_str = time_str($time);
            $data['statis']['totalYDl'] =[
                'getDirectlyYaAttribute'    =>
                    $user->getDirectlyYaAttribute($time_str),
                'getDirectlyDeAttribute'    =>
                    $user->getDirectlyDeAttribute($time_str),
                'getDirectlyYlAttribute'    =>
                    $user->getDirectlyYlAttribute($time_str),
    
                'getDirectlyNotYa'    =>
                    $user->getDirectlyNotYa($time_str),
                'getDirectlyNotDe'    =>
                    $user->getDirectlyNotDe($time_str),
                'getDirectlyYlNot'    =>
                    $user->getDirectlyYlNot($time_str),
            ];
        } else {
            $data['statis']['totalYDl'] =[
                'getDirectlyYaAttribute'    =>
                    $user->getDirectlyYaAttribute(),
                'getDirectlyDeAttribute'    =>
                    $user->getDirectlyDeAttribute(),
                'getDirectlyYlAttribute'    =>
                    $user->getDirectlyYlAttribute(),
    
                'getDirectlyNotYa'    =>
                    $user->getDirectlyNotYa(),
                'getDirectlyNotDe'    =>
                    $user->getDirectlyNotDe(),
                'getDirectlyYlNot'    =>
                    $user->getDirectlyYlNot(),
            ];
        }
        //图形显示 ,需要单独的处理
        return $this->success($data);
    }
    /**
     * 营收报表的统计 function
     *
     * @return void
     */
    public function indexCharts()
    {
        $AgentReport = AgentReport::select('time as School Year', 'status', 'directly_yl as value')
                            ->where('status', 3)
                            ->where('uid', $this->userInfoBase()->email)
                            ->orderBy('id', 'DESC')
                            ->limit(30)
                            ->get()
                            ->toArray();

        foreach ($AgentReport as $k => $v) {
            if ($v['status']) {
                $AgentReport[$k]['type'] = '直属总盈';
            }
            if (empty($v['value'])) {
                $AgentReport[$k]['value'] = 0;
            }

            $AgentReport[$k]['School Year'] = substr($v['School Year'], 5, 5);
            unset($AgentReport[$k]['status']);
        }


        $AgentReportNot = AgentReport::select('time as School Year', 'status', 'directly_not_yl as value')
                                ->where('status', 3)
                                ->where('uid', $this->userInfoBase()->email)
                                ->orderBy('id', 'DESC')
                                ->limit(30)
                                ->get()
                                ->toArray();


        foreach ($AgentReportNot as $k => $v) {
            if ($v['status']) {
                $AgentReportNot[$k]['type'] = '非直属总盈';
            }
            if (empty($v['value'])) {
                $AgentReportNot[$k]['value'] = 0;
            }
            $AgentReportNot[$k]['School Year'] = substr($v['School Year'], 5, 5);


            unset($AgentReportNot[$k]['status']);
        }

        return $this->success(array_merge($AgentReport, $AgentReportNot));
    }

    /**
     * 玩家管理概况--报表 function
     *
     * @return void
     */
    public function playerCharts(Request $request)
    {
        $user_id = $request->user_id;
        if ($user_id) {
            $user = DailyBills::where('agent_id',$user_id)->select('id','off_line','online','time','type')->orderBy('id','DESC')->limit(30)->get()->toArray();
            $data = [];

            foreach ($user as $k => $v) {
                $data[$k]['School Year'] = substr($v['time'],5,5);
                $data[$k]['type'] = '当月充值';
                $data[$k]['value'] = $v['off_line'] +  $v['online'];
            }

            return $this->success($data);
        }
        return $this->failed('failed');
    }
    /**
     * 代理管理---对应的管理ID下的直属情况. 单个时间搜索 function
     *
     * @return void
     */
    public function agent(Request $request)
    {
        //总充值 总赠送 总兑换，总押 总得 总盈得 总抽水  每天充值情况 显示最近一月
        $user_id = $request->user_id;
        $time = $request->time;

        if ($user_id) {
            $user =  UserSimple::where('email', $user_id)->first();
            $timeData =[];

            if ($time) {
                $timeData['yesterday'] = $time;
                $timeToday = strtotime($time) + 86400;
                $timeData['today']  = date('Y-m-d 00:00:00', $timeToday);
            } else {
                $time = false;
            }
            $recordDetail =  $user->DirectlyRecordDetail($timeData ?? false, $user_id);
            if ($recordDetail) {
                $recordDetail = json_decode($recordDetail, true);
            }
            $coins = ApiConis::where('manager_id', $user->id)->sum('coins');

            $data = [
                'paraent_id'    => $user->bind_id == 0 ? '顶级代理' : $user->bind_id,
                'give'  => $user->amount ?? 0,
                'total_order'   =>
                    $user->getDirectlyOrderAttribute(false, $user_id) ,
                'total_cashs'   =>
                    $user->getDirectlyCashsAttribute($timeData ?? false, $user_id) ,
                'total_coins'   =>  $coins ?? 0,
                'total_yazhu'   => $recordDetail[0]['yazhu'] ?? 0,
                'total_defen'   => $recordDetail[0]['defen'] ?? 0,
                'total_yingli'   => $recordDetail[0]['yingli'] ?? 0,
                'total_choushui'   => $recordDetail[0]['choushui'] ?? 0,
            ];

            return $this->success($data);
        }
        return $this->failed('failed');
    }
    /**
     * 代理图表显示  function
     *
     * @return void
     */
    public function agentCharts(Request $request)
    {
        $user_id = $request->user_id;
        if ($user_id) {
            $data = [];
            $userDaily = DailyBills::where('agent_id',$user_id)->select('id','off_line','online','time','type')->where('type','total')->orderBy('id','DESC')->limit(30)->get()->toArray();

            foreach ($userDaily as $k => $v) {
                $data[$k]['School Year'] = substr($v['time'],5,5);
                $data[$k]['type'] = '当月充值';
                $data[$k]['value'] = $v['off_line'] +  $v['online'];
            }
            return $this->success($data);
        }
        return $this->failed('failed');
    }

}
