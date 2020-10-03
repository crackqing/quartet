<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Models\Game\RecordDetail;
use App\Models\Game\RecordDetailStatis as Record;
use App\UserSimple;

class RecordStatis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statis:record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'statis player data';

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
        //统计每日的游戏数据 排除练习厅的话tid 就要统一为一个数字 999
        
        $yesterDay = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $today = date('Y-m-d 00:00:00', time());
        $data = [
            'yazhuTotal' => 0,
            'defenTotal' => 0,
            'yingliTotal' => 0,
            'choushuiTotal' => 0,
            'coinsTotal' => 0,
            'bankTotal' => 0,
            'kindid'    => 10000,
            'time'  => $yesterDay
        ];
        $recordData = [];

        $record = RecordDetail::whereBetween('time', [$yesterDay,$today])
            ->select(
                DB::raw('SUM(yazhu) as yazhuTotal'),
                DB::raw('SUM(defen) as defenTotal'),
                DB::raw('SUM(yingli) as yingliTotal'),
                DB::raw('SUM(choushui) as choushuiTotal'),
                DB::raw('SUM(coins) as coinsTotal'),
                DB::raw('SUM(bank) as bankTotal'),
                'kindid',
                'time'
            )
            ->groupBy('kindid')
            ->get()
            ->toArray();

        #单个游戏的存放 与 总合的计算 总合的kindid 10000
        foreach ($record as $k => $v) {
            $data['yazhuTotal'] += $v['yazhuTotal'];
            $data['defenTotal'] += $v['defenTotal'];
            $data['yingliTotal'] += $v['yingliTotal'];
            $data['choushuiTotal'] += $v['choushuiTotal'];
            $data['coinsTotal'] += $v['coinsTotal'];
            $data['bankTotal'] += $v['bankTotal'];

            $v['time'] = $yesterDay;
            Record::create($v);
        }
        #游戏管理--游戏记录总统计 10000
        Record::create($data);



        #单个玩家总统,用于指定绑定时上级的查询
        $recordUser = RecordDetail::whereBetween('time', [$yesterDay,$today])
            ->select(
                DB::raw('SUM(yazhu) as yazhuTotal'),
                DB::raw('SUM(defen) as defenTotal'),
                DB::raw('SUM(yingli) as yingliTotal'),
                DB::raw('SUM(choushui) as choushuiTotal'),
                DB::raw('SUM(coins) as coinsTotal'),
                DB::raw('SUM(bank) as bankTotal'),
                'uid',
                'time'
            )
            ->groupBy('uid')
            ->get()
            ->toArray();
        foreach ($recordUser as $k => $v) {
            $v['time'] = $yesterDay;
            Record::create($v);
        }

        #单个玩家的计算的单个游戏统计情况,用于指定绑定时上级的查询
        $recordUserKindid = RecordDetail::whereBetween('time', [$yesterDay,$today])
            ->select(
                DB::raw('SUM(yazhu) as yazhuTotal'),
                DB::raw('SUM(defen) as defenTotal'),
                DB::raw('SUM(yingli) as yingliTotal'),
                DB::raw('SUM(choushui) as choushuiTotal'),
                DB::raw('SUM(coins) as coinsTotal'),
                DB::raw('SUM(bank) as bankTotal'),
                'uid',
                'time',
                'kindid'
            )
            ->groupBy('uid', 'kindid')
            ->get()
            ->toArray();
        foreach ($recordUserKindid as $k => $v) {
            $v['time'] = $yesterDay;
            Record::create($v);
        }
        #统计后台代理的直属与非直属的情况单个游戏 与直属 非直属的数据展示 
        // $user = UserSimple::where('manager', 1)->get();
        // foreach ($user as $k => $v) {
        //     if (!isset($v->email)) {
        //         continue;
        //     }
        //     if (mb_strlen($v->email) > 7) {
        //         continue;
        //     }
        //     $recordUserKindid = RecordDetail::whereBetween('time', [$yesterDay,$today])
        //     ->select(
        //         DB::raw('SUM(yazhu) as yazhuTotal'),
        //         DB::raw('SUM(defen) as defenTotal'),
        //         DB::raw('SUM(yingli) as yingliTotal'),
        //         DB::raw('SUM(choushui) as choushuiTotal'),
        //         DB::raw('SUM(coins) as coinsTotal'),
        //         DB::raw('SUM(bank) as bankTotal'),
        //         'uid',
        //         'time',
        //         'kindid'
        //     )
        //     ->groupBy('uid', 'kindid')
        //     ->get()
        //     ->toArray();
        //     foreach ($recordUserKindid as $k => $v) {
        //         $v['type'] = 'total';
        //         $v['time'] = $yesterDay;
        //         Record::create($v);
        //     }
        //     $data = [
        //         'directly_pay'   => $v->getDirectlyOrderAttribute($time,$v->email),
        //         'directly_not_pay'  => $v->getDirectlyNotPlayerOrderAttribute($time,$v->email),
        //         'status'    => 1,
        //         'uid'   =>  (int) $v->email,
        //         'time'  => $time['yesterday']
        //     ];
        // }


    }
}
