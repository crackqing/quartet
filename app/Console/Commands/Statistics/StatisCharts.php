<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;
use App\Models\Game\Higcharts;
use Illuminate\Support\Facades\Redis;
use App\User;
use Illuminate\Support\Carbon;

class StatisCharts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charts:statis';

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
        #昨天 与今天的新增 实时更新。updateOrCreate 更新DB数据60天的留存数据
        $todayParse = Carbon::parse('today')->format('Y-m-d H:i:00');
        $todayParseEnd =  Carbon::parse('today')->format('Y-m-d 23:59:59');


        #新增绑定玩家的数量计算
        $yesterday = date('Ymd', strtotime('-1 day'));
        $today = date('Ymd', time());

        $yesterdayNew = date('Ymd', strtotime('-1 day')).'new';
        $todayNew = date('Ymd', time()).'new';

        $yesterdayRedisLogin = Redis::command('bitcount', [$yesterday.':']);
        $yesterdayRedisNew = Redis::command('bitcount', [$yesterdayNew]);

        $todayRedisLogin = Redis::command('bitcount', [$today.':']);
        $todayRedisNew = Redis::command('bitcount', [$todayNew]);
        
        $higcharts =  Higcharts::where('date', $today)->first();

        $bindCount = User::where('bind_id','!=',80000)
                    ->whereBetween('created_at',[$todayParse,$todayParseEnd])
                    ->where('manager','!=',1)
                    ->count();
        if ($higcharts) {
            Higcharts::where('date', $today)->update(['new'=>$todayRedisNew,'login'=> $todayRedisLogin,'bind' => $bindCount]);
        } else {
            Higcharts::create(['date' => $today,'new'=>$todayRedisNew,'login'=> $todayRedisLogin,'bind' => $bindCount]);
        }
        Higcharts::updateOrCreate(['date' => $yesterday,'new'=>$yesterdayRedisNew,'login'=> $yesterdayRedisLogin,'bind' => $bindCount]);
    }
}
