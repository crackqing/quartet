<?php

namespace App\Console\Commands\Email;

use Illuminate\Console\Command;
use App\Models\Game\DailyChoushui as daily;
use DB,Mail,Config;
use Illuminate\Support\Carbon;
use App\Models\Game\RecordDetail;

class CrontabEmailDaily extends Command
{
    /**
     * The name and signature of the console command.
     *  每日的定时邮件发送
     * @var string
     */
    protected $signature = 'daily:sendEmail';

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
        $time = date('Y-m-d',strtotime('-1 days'));
        $daily = daily::select(
            'id',
            DB::raw('SUM(total_choushui) as total_choushui'),
            'time_rand',
            'bind_id',
            'time'
        )
        ->where('time',$time)
        ->orderBy('total_choushui','DESC')
        ->groupBy('bind_id','time')
        ->get();
        $data = [
            '18789506429@139.com',
            'moab1935@Foxmail.com',
        ];
        foreach ($data as $k => $v) {
            #读取配置文件中里面的邮件地址,批量发送.
            Mail::send('emails.daily_pumping',['daily' => $daily],function ($message) use ($v){
                $to = $v;
                $message->to($to)->subject('流水排行'.date('Y-m-d',strtotime('-1 days')));
            });
        }


    }
}


