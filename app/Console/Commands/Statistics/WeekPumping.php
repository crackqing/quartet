<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;

use App\UserSimple;
use Illuminate\Support\Carbon;
use App\Models\Game\DailyChoushuiAgent;
use App\Models\Game\WeekChoushuiAgent;
use DB;
use Log;

class WeekPumping extends Command
{
    /**
     * The name and signature of the console command.
     *      每周的抽水统计,用于代理的计算
     * @var string
     */
    protected $signature = 'week:pumping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
            1.每周的凌晨10分开始统计数据,计算每个代理直属代理用户的数值.
                1.1 已经计算出了佣金7天内的数据 * 对应的比率就行.
                1.2

            2.非直属的计算,是对应的代理绑定下面的代理用户 层级关系也需要计算.

            return_gold     个人业绩 里面的是按每日来算的,所以这里不能单独的只是SUM加起来
        */

        #实发钱数 ,层级累计到上级，  上级团队 -- 返回 ==  实发
        $user = UserSimple::where('manager', 1)
                            ->select('name', 'email', 'bind_id')
                            ->get();
        $startWeek = Carbon::parse('yesterday')->format('Y-m-d 00:00:00'); //yesterday
        $endWeek =Carbon::parse('yesterday')->format('Y-m-d 23:59:59');


        #个人业绩需要加起来计算..这周的计算。 每个人 FIX:修改成每一个天的计算
        $dailyReturnGold = DailyChoushuiAgent::select(
            'id',
            DB::raw('SUM(total_choushui) as total_choushui'),
            'time_rand',
            'bind_id',
            'time'
        )
        ->whereBetween('time', [$startWeek,$endWeek])
        ->groupBy('bind_id', 'time_rand')
        ->get();
        $dataReturnBindId = [];
        foreach ($dailyReturnGold as $k => $v) {
            if ($v->bind_id == 0) {
                continue;
            }
            #计算值
            // if (isset($dataReturnBindId[$v->bind_id])) {
            //     $level = agent_daily_percentage($v->total_choushui);

            //     $dailySource = $level[1] * $v->total_choushui;

            //     $dataReturnBindId[$v->bind_id] += $dailySource;
            // } else {
            //     $level = agent_daily_percentage($v->total_choushui);
            //     $dailySource = $level[1] * $v->total_choushui;
            //     $dataReturnBindId[$v->bind_id] = $dailySource;
            // }

            if (isset($dataReturnBindId[$v->bind_id])) {
                $level = agent_daily($v->total_choushui);

                $dailySource = floor($v->total_choushui / 10000 ) * $level[1] ;

                $dataReturnBindId[$v->bind_id] += $dailySource;
            } else {
                $level = agent_daily($v->total_choushui);
                $dailySource = floor($v->total_choushui / 10000 ) * $level[1] ;
                $dataReturnBindId[$v->bind_id] = $dailySource;
            }

        }
        #个人业绩已领钱数
        $relation = user_relation_all(0);

        foreach ($user as $k => $v) {
            $agentDirectlyId = $v->getAgentDirectlyIdAttribute();

            Log::info('agentDirectlyId===>', ['agentDirectlyId'=> $agentDirectlyId,'email'=>$v->email]);

            $daily = [];
            #直属本身也需要加入进来
            if (!empty($agentDirectlyId)) {
                $daily = DailyChoushuiAgent::select(
                    DB::raw('SUM(total_choushui) as total_choushui'),
                    'level'
                )
                    ->whereBetween('time', [$startWeek,$endWeek])
                    ->whereRaw(DB::raw("bind_id IN ($agentDirectlyId,$v->email)"))
                    ->get();
            } else {
                $daily = DailyChoushuiAgent::select(
                    DB::raw('SUM(total_choushui) as total_choushui'),
                    'level'
                )
                    ->whereBetween('time', [$startWeek,$endWeek])
                    ->where('bind_id', $v->email)
                    ->get();
            }
            if (is_object($daily)) {
                $data = [
                    'time_rand' => $startWeek .' - '. $endWeek,
                    'uid'   => $v->email,
                    'time'  => date('Y-m-d 00:00:00'),
                ];
                foreach ($daily as $k2 => $d) {
                    $choushui = $d->total_choushui  ?? 0;
                    
                    $return_gold = 0;
                    #个人的业绩--这周每天的计算
                    if (isset($dataReturnBindId[$v->email])) {
                        $return_gold = $dataReturnBindId[$v->email];
                    }
                    #团队业绩 =  所有的直属代理抽水 +自己抽水
                    #团队应发佣金 =  团队业绩/500取整，然后乘以对于的代理等级系数 (业绩得出)

                    #个人业绩 = 自己抽水
                    #个人佣金 (实发)= 个人业绩 / 500 取整 * 对应的个人业绩 系数

                    #团队已发佣金 = 自己个人佣金 + 下级所有代理的实发团队佣金 + 下级所有代理的个人佣金
                    #团队实发佣金 = 团队应发佣金 - 团队已发佣金

                    #团队
                    $team = agent_daily($choushui);
                    // $teamSource =  $choushui ;

                    #个人
                    // $personal = agent_daily_percentage($choushui);
                    // $personalSource =  $choushui  ;
                    $payable =  floor($choushui / 10000 ) * $team[1] - $return_gold;

                    $levelNumber = 50;
                    $bind_id = 0;
                    foreach ($relation as $k2 => $v2) {
                        if ($v2['email'] == $v->email ) {
                            $levelNumber = $v2['level'];
                            $bind_id = $v2['bind_id'];
                        }
                    }
                    $data['performance'] = $team[1];
                    $data['multiple'] = 0;
                    $data['return_gold'] =  floor($choushui / 10000 ) * $team[1];

                    $data['total_choushui'] = $choushui ;
                
                    $data['receive'] = $return_gold ;
                    #增加层级的计算
                    $data['level'] = $levelNumber;
                    $data['bind_id'] = $bind_id ?? 0;
                    //实时发放的钱数 要 - 去个人每日所得的值
                    if ($payable > 0) {
                        $data['payable'] = $payable;
                    } else {
                        $data['payable'] = 0;
                    }
                    WeekChoushuiAgent::create($data);
                }
            }
            unset($daily,$agentDirectlyId);
        }
        #实发钱数 ,用绑定ID的层级来计算---返利钱数 - 自身所有绑定ID。
        $weekPay = WeekChoushuiAgent::where('time_rand', $startWeek .' - '. $endWeek)->get();
        foreach ($weekPay as $k => $v) {
             $weekPayStartWeekSum = WeekChoushuiAgent::where('time_rand', $startWeek .' - '. $endWeek)
                                            ->where('bind_id',$v->uid)
                                            ->sum('return_gold');
            $v->team_receive = $v->receive + $weekPayStartWeekSum;
            $payable = $v->return_gold -  ($v->receive + $weekPayStartWeekSum);
            if ($payable < 0) {
                $v->payable = 0;
            } else {
                $v->payable = $payable;
            }
            $v->save();
            unset($weekPayStartWeekSum);
        }
    }
}
