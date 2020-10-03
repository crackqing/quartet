<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;

use App\Models\Game\Orders;
use App\Models\Game\Cashs;
use App\UserSimple;

use App\Models\Game\AgentReport;

/**
 *  人员管理--代理信息  充值报表 与兑换报表  游戏记录 晚上凌晨统计.
 */
class RecordDailyAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statis:agent';

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
     * 1为充值，2为兑换，3为游戏记录 赠送记录待定 the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time = [
            'yesterday' => date('Y-m-d 00:00:00', strtotime('-1 day')),
            'today' => date('Y-m-d 00:00:00')
        ];
        #获取顶级代理下面的数据计算--充值
        $user = UserSimple::where('manager', 1)->get();

        foreach ($user as $k => $v) {
            if (!isset($v->email)) {
                continue;
            }
            if (mb_strlen($v->email) > 7) {
                continue;
            }

            $data = [
                'directly_pay'   => $v->getDirectlyOrderAttribute($time,$v->email),
                'directly_not_pay'  => $v->getDirectlyNotPlayerOrderAttribute($time,$v->email),
                'status'    => 1,
                'uid'   =>  (int) $v->email,
                'time'  => $time['yesterday']
            ];
            AgentReport::create($data);
        }
        #获取顶级代理下面的数据计算--兑换
        foreach ($user as $k => $v) {
            if (!isset($v->email)) {
                continue;
            }

            if (mb_strlen($v->email) > 7) {
                continue;
            }
            $data = [
                'directly_pay'   => $v->getDirectlyCashsAttribute($time,$v->email),
                'directly_not_pay'  => $v->getDirectlyNotPlayerCashsAttribute($time,$v->email),
                'status'    => 2,
                'uid'   => (int) $v->email,
                'time'  => $time['yesterday']

            ];
            AgentReport::create($data);
        }
        #总押，总得，总盈得 ，直属与非直属的计算.
        $dataRecod =  [];
        foreach ($user as $k => $v) {
            if (!isset($v->email)) {
                continue;
            }

            if (mb_strlen($v->email) > 7) {
                continue;
            }
            $recordDirectly = $v->DirectlyRecordDetail($time,$v->email);
            if (!empty($recordDirectly)) {
                foreach ($recordDirectly as $k2 => $v2) {
                    $dataRecod = [
                        'directly_ya'   => $v2->yazhu,
                        'directly_de'   => $v2->defen,
                        'directly_yl'   => $v2->yingli,
                        'status'    => 3,
                        'uid'   => (int) $v->email,
                        'time'  => $time['yesterday']

                    ];
                }
            }
            $dataRecod2 =[];
            //非直属的，需要单个上级ID传入。才能知道对应的下级层级
            $recordDirectlyNot = $v->DirectlyNotRecordDetail($time, $v->email);


            if (!empty($recordDirectlyNot)) {
                foreach ($recordDirectlyNot as $k3 => $v3) {
                    $dataRecod2 = [
                        'directly_not_ya'   => $v3->yazhu,
                        'directly_not_de'   => $v3->defen,
                        'directly_not_yl'   => $v3->yingli,
                        'status'    => 3,
                        'uid'   => (int) $v->email ,
                         'time'  => $time['yesterday']

                    ];
                }
            }
            $dataMerge = array_merge($dataRecod, $dataRecod2);
            // dump($dataRecod,$v->email,$recordDirectly,$recordDirectlyNot);
            AgentReport::updateOrCreate($dataMerge);
            $dataRecod= [];
        }



        $dataRecodKindid =  [];
        foreach ($user as $k => $v) {
            if (!isset($v->email)) {
                continue;
            }

            if (mb_strlen($v->email) > 7) {
                continue;
            }
            $recordDirectly = $v->DirectlyRecordDetailKindid($time,$v->email);
            if (!empty($recordDirectly)) {
                foreach ($recordDirectly as $k2 => $v2) {
                    $dataRecodKindid = [
                        'directly_ya'   => $v2->yazhu,
                        'directly_de'   => $v2->defen,
                        'directly_yl'   => $v2->yingli,
                        'status'    => 4,
                        'uid'   => (int) $v->email,
                        'time'  => $time['yesterday'],
                        'kindid'    => $v2->kindid,
                    ];

                    AgentReport::create($dataRecodKindid);
                }
            }
            $dataRecodKindid2 =[];
            //非直属的，需要单个上级ID传入。才能知道对应的下级层级
            $recordDirectlyNot = $v->DirectlyNotRecordDetailKindid($time, $v->email);


            if (!empty($recordDirectlyNot)) {
                foreach ($recordDirectlyNot as $k3 => $v3) {
                    $dataRecodKindid2 = [
                        'directly_not_ya'   => $v3->yazhu,
                        'directly_not_de'   => $v3->defen,
                        'directly_not_yl'   => $v3->yingli,
                        'status'    => 4,
                        'uid'   => (int) $v->email ,
                        'time'  => $time['yesterday'],
                        'kindid'    => $v2->kindid,

                    ];
                    #更新本条游戏 对应的非直属情况
                    AgentReport::where('uid',$v->email)
                        ->where('kindid',$v2->kindid)
                        ->where('time',$time['yesterday'])
                        ->update($dataRecodKindid2);
                }
            }
            $dataRecodKindid= [];
        }

    }
}
