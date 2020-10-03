<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;
use App\UserSimple;
use App\Models\Game\RecordDetailIntval;
use App\Models\Game\DailyChoushui as dailyUid;
use App\Models\Game\DailyChoushuiAgent as dailyAgent;

use Illuminate\Support\Carbon;
use DB;
use App\User;

class dailyPumping extends Command
{
    /**
     * The name and signature of the console command.
     *      每日抽水（流水）的所有玩家统计计算
     * @var string
     */
    protected $signature = 'daily:pumping';

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

        $yesterday = Carbon::parse('yesterday')->format('Y-m-d H:i:00');
        $yesterdayEnd =  Carbon::parse('yesterday')->format('Y-m-d 23:59:59');

        $record = RecordDetailIntval::select('kindid','uid','yingli','time')
                                ->whereBetween('time', [$yesterday,$yesterdayEnd])
                                ->get();
        $recordCollect = collect($record);
        $recordCollectUid = collect($record);
        /**
            1.原先的计算为 ===>业绩---抽水(choushui) * 20

            1.1现在的计算为 ===>业绩---yingli (绝对值  intval) 求和 (sum)
         
          游玩记录中针对游戏类别为1100（捕鱼）的盈利值，需要在基础数据除以10000后再除以10，也就是说一共除以100000
         */
        #总的单个绑定ID统计,后面应该加上单个UID的统计 groupBy('uid')
        $recordCollect = $recordCollect->groupBy('bindId');
        foreach($recordCollect as $k => $v){
            $yingli = $v->sum('yingli');
            $level = agent_daily_percentage($yingli);


            $data = [
                'uid'   => 100000,
                'time_rand'   => $yesterday .' - ' . $yesterdayEnd ,
                'total_choushui'   => $yingli ,
                'is_dealer'   =>  0,
                'performance'   => $level[1],
                'multiple'      => 0,
                'return_gold'   => $yingli  * $level[1],
                'bind_id'   => $k ?? 0,
                'time'   => $yesterday,
                'level' => 0
            ];
            #level 等级排序查询处理，level为1的先算 orderBy DESC
            dailyAgent::create($data);
            unset($yingli);  
        }
        #单个UID的处理方式
        $recordCollectUid = $recordCollectUid->groupBy('uid');
        foreach ($recordCollectUid as $k => $v) {
            $yingli = $v->sum('yingli');
            $level = agent_daily_percentage($yingli);

            $bind = User::where('agent_id',$k)->value('bind_id');
            $data = [
                'uid'   => $k,
                'time_rand'   => $yesterday .' - ' . $yesterdayEnd ,
                'total_choushui'   => $yingli ,
                'is_dealer'   =>  0,
                'performance'   => $level[1],
                'multiple'      => 0,
                'return_gold'   => $yingli  * $level[1],
                'bind_id'   => $bind ?? 0 ,
                'time'   => $yesterday,
            ];
            dailyUid::create($data);
            unset($yingli);  
        }


    }
}
