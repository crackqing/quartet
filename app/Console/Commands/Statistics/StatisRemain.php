<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;
use App\Models\Game\Higcharts;
use Illuminate\Support\Facades\Redis;

class StatisRemain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charts:remain';

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
     * 留存的处理，只取最近的40条数据 command.
     *
     * @return mixed
     */
    public function handle()
    {
        $highcharts = Higcharts::orderBy('id', 'DESC')->limit(30)->get();

        foreach ($highcharts as $h) {
            $date = substr(str_replace('-', '', $h->date), 0, 8).'new';

            #当前留存的人数 / bitcount 留存率
            $number = Redis::command('bitcount', [$date]);
            
            #明日留存
            $tomorrow = strtotime($h->date) +  86400;
            $tomorrow = date('Ymd', $tomorrow).':';

            $bigtopTomorrow = $date .'and'.$tomorrow;
            Redis::command('bitop', ['and',$bigtopTomorrow,$date,$tomorrow]);
            $bitCountTomorrow = Redis::command('bitcount', [$bigtopTomorrow]);

            #第三日留存
            $three = strtotime($h->date) +  259200;
            $three = date('Ymd', $three).':';

            $bigtopTree = $date .'and'.$three;
            Redis::command('bitop', ['and',$bigtopTree,$date,$three]);
            $bitCountTree = Redis::command('bitcount', [$bigtopTree]);

            #第7日留存
            $sevenDays = strtotime($h->date) +  604800;
            $sevenDays = date('Ymd', $sevenDays).':';

            $bigtopSeven = $date .'and'.$sevenDays;
            Redis::command('bitop', ['and',$bigtopSeven,$date,$bigtopSeven]);
            $bitCountSeven = Redis::command('bitcount', [$bigtopSeven]);
            
            #留存率-- 次日为 昨日/今日 * 100;
            $h->remain_1 = $bitCountTomorrow / $number * 100;
            $h->remain_3 =$bitCountTree / $number * 100;
            $h->remain_7 =$bitCountSeven / $number * 100;
        
            $h->save();
        }
    }
}
