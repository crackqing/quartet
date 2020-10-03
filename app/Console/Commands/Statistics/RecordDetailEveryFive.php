<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;

use App\Models\Game\RecordDetail; 
use App\Models\Game\RecordDetailEvery;

use DB;

class RecordDetailEveryFive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statis:everyFive';

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
        #时间区间会算重复 需要减去1秒进行处理
        $starTime = date('Y-m-d H:i:59',time() - 60);
        $endTime = date('Y-m-d H:i:00',time() - 300);

        $data = [
            'yazhu' => 0,
            'defen' => 0,
            'choushui' => 0,
            'kindid'    => 10000,
            'tid'  => 0,
            'start_time' => $starTime,
            'end_time'   => $endTime
        ]; 
        /**
         * 每五分记录单个游戏的每个桌子的情况，都需要存放记录,并设置为五分.
         */
        $record = RecordDetail::whereBetween('time',[$endTime,$starTime])
            ->select(
                DB::raw('SUM(yazhu) as yazhuTotal'),
                DB::raw('SUM(defen) as defenTotal'),
                DB::raw('SUM(choushui) as choushuiTotal'),
                'kindid','tid'               
            )
            ->groupBy('kindid','tid')
            ->get()
            ->toArray();
        foreach($record as $v){
            $data['yazhu'] = $v['yazhuTotal'];
            $data['defen'] = $v['defenTotal'];
            $data['choushui'] = $v['choushuiTotal'];
            $data['kindid'] = $v['kindid'];
            $data['tid']    = $v['tid'];
            $data['start_time'] = $endTime;
            $data['end_time'] = $starTime;

            RecordDetailEvery::create($data);
        }
    }
}
