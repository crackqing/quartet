<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;
use App\Models\Game\RecordDetail; 
use App\Models\Game\RecordSingle as single; 
use App\Models\Game\RecordSingleKindid as singleKindid; 

use Illuminate\Support\Facades\DB;

class RecordUserSingle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statis:singleUser';

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
        $starTime = date('Y-m-d H:i:59',time() - 60);
        $endTime = date('Y-m-d H:i:00',time() - 300);


        //yazhuTotal,defenTotal,yingliTotal,choushuiTotal,coinsTotal,bankTotal,time,uid,c,u
        $record = RecordDetail::whereBetween('time',[$endTime,$starTime])
            ->select(
                DB::raw('SUM(yazhu) as yazhuTotal'),
                DB::raw('SUM(defen) as defenTotal'),
                DB::raw('SUM(yingli) as yingliTotal'),
                DB::raw('SUM(choushui) as choushuiTotal'),
                DB::raw('SUM(coins) as coinsTotal'),
                DB::raw('SUM(bank) as bankTotal') ,
                'uid'               
            )
            ->groupBy('uid')
            ->get()
            ->toArray();
        #取游戏余额 与保险箱余额  详细查看取的是time
        foreach($record as $k => $r){
            $singleUserDesc = RecordDetail::whereBetween('time',[$endTime,$starTime])->where('uid',$r['uid'])->orderBy('id','DESC')->limit(1)->first();

            $record[$k]['gameBalance'] = $singleUserDesc->coins;
            $record[$k]['bankBalance'] = $singleUserDesc->bank;
            $record[$k]['timeRange'] = $endTime.' - '.$starTime;
            $record[$k]['type'] = 'singleUid';
        }
        foreach($record as $k => $r){
            single::create($r);
        }


        #单个游戏10分的统计单个用户的情况，不分开操作.
        $recordKindid = RecordDetail::whereBetween('time',[$endTime,$starTime])
            ->select(
                DB::raw('SUM(yazhu) as yazhuTotal'),
                DB::raw('SUM(defen) as defenTotal'),
                DB::raw('SUM(yingli) as yingliTotal'),
                DB::raw('SUM(choushui) as choushuiTotal'),
                DB::raw('SUM(coins) as coinsTotal'),
                DB::raw('SUM(bank) as bankTotal') ,
                'uid','kindid'               
            )
            ->groupBy('uid','kindid')
            ->get()
            ->toArray();
        #取游戏余额 与保险箱余额  详细查看取的是time
        foreach($recordKindid as $k => $r){
            $singleUserDesc = RecordDetail::whereBetween('time',[$endTime,$starTime])->where('uid',$r['uid'])->orderBy('id','DESC')->limit(1)->first();

            $recordKindid[$k]['gameBalance'] = $singleUserDesc->coins;
            $recordKindid[$k]['bankBalance'] = $singleUserDesc->bank;
            $recordKindid[$k]['timeRange'] = $endTime.' - '.$starTime;
            $recordKindid[$k]['type'] = 'singleKindid';

        }
        foreach($recordKindid as $k => $r){
            single::create($r);
        }

    }
}
