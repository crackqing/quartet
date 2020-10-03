<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Table\SyncData2api',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {      
        #定时同步游戏表数据
//        $schedule->command('sync:table')->everyMinute();

        #每五分同步单个游戏里面的桌子数据 
//        $schedule->command('statis:everyFive')->everyFiveMinutes();
                
        #昨天的统计，玩家数据 与游戏数据  ,与每10分统计单个玩家的游戏数据
//        $schedule->command('statis:record')->daily();
//        $schedule->command('statis:singleUser')->everyFiveMinutes();

        #每五统计新增，与日活。 昨天与今天的数据.  30分更新留存，次留这些处理.
//        $schedule->command('charts:statis')->everyFiveMinutes();
//        $schedule->command('charts:remain')->dailyAt('00:08');

        #每日抽水单个用户玩家--代理盈利YINGLI分成，分钱给代理
//        $schedule->command('daily:pumping')->dailyAt('00:06');
//        $schedule->command('week:pumping')->dailyAt('00:11');

        #每小时更新代理的名称与封号状态，金钱数量==
//        $schedule->command('batch:updateUser')->hourly();

        #统计----每日账单 与 每日代理的充值 兑换 游戏记录报告
//        $schedule->command('statis:daily')->daily();
//        $schedule->command('statis:agent')->daily();

        #定时批量备份线上DB 与redis 数据 rsync 同步到多台与单台服务器
        // $schedule->command('db:backup')->dailyAt('05:05');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
