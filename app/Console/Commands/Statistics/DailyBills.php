<?php

namespace App\Console\Commands\Statistics;

use Illuminate\Console\Command;

use App\Models\Game\Cashs;
use App\Models\Game\Orders;
use App\Models\Server\ApiConis;
use App\Models\Game\DailyBills as daily;
use App\Service\QpApi;
use App\User;

class DailyBills extends Command
{
    public $api ;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statis:daily';

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
    public function __construct(QpApi $api)
    {
        parent::__construct();

        $this->api = $api;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //每日账单 处理  （所有充值，线上，线下， 赠送，兑换， 金额） 这是统计所有的情况。 默认80000进来看到的.  30 31的单个用户有问题，其它时间段正常
        $yesterday = date('Y-m-d H:00:00', strtotime('-1 day'));
        $today = date('Y-m-d H:00:00');

        $cashs = Cashs::whereBetween('created_at', [$yesterday,$today])
                    ->whereIn('status', [2,3,5])
                    ->sum('cash_money') / 100;

        $ordersOffline = Orders::whereBetween('created_at', [$yesterday,$today])
                    ->where('pay_type', '线下充值')
                    ->sum('price') / 100;
        $ordersOnline  = Orders::whereBetween('created_at', [$yesterday,$today])
                    ->where('pay_type', '!=', '线下充值')
                    ->sum('price') / 100;

        $ApiConis = ApiConis::whereBetween('created_at', [$yesterday,$today])
                    ->sum('coins');

        $totalCoin = $this->api->getCoins();
        $totalCoins =  (int) $totalCoin['totalCoins'];
        $totalBanks =  (int) $totalCoin['totalBanks'];
        //'agent_id'  => 100000,  为所有的查询显示
        $data = [
            'off_line'  => $ordersOffline ?? 0,
            'online'    => $ordersOnline ?? 0,
            'coins' => (int) $ApiConis ?? 0,
            'cashs' => $cashs ?? 0,
            'account'   => $totalCoins ?? 0,
            'bank'  => $totalBanks ?? 0,
            'type'  => 'total',
            'agent_id'  => 100000,
            'time'  => $yesterday
        ];
        daily::create($data);

        //应该把单个玩家的情况也统计起来。 充值 线下充值 ，赠送 ==操作  type total single
        $user =  User::where('manager', 0)->get();
        foreach ($user as $k => $v) {
            $cashs = Cashs::whereBetween('created_at', [$yesterday,$today])
                        ->whereIn('status', [2,3,5])
                        ->where('agent_id', $v->agent_id)
                        ->sum('cash_money') / 100;

            $ordersOffline = Orders::whereBetween('created_at', [$yesterday,$today])
                        ->where('agent_id', $v->agent_id)
                        ->where('pay_type', '线下充值')
                        ->sum('price') / 100;
            $ordersOnline = Orders::whereBetween('created_at', [$yesterday,$today])
                        ->where('agent_id', $v->agent_id)
                        ->where('pay_type', '!=', '线下充值')
                        ->sum('price') / 100;
            $ApiConis = ApiConis::whereBetween('created_at', [$yesterday,$today])
                        ->where('uid', $v->agent_id)
                        ->sum('coins');


            $user = $this->api->getUserInfo($v->agent_id);

            $dailyUserSignle = [
                'off_line'  => $ordersOffline ?? 0,
                'online'    => $ordersOnline ?? 0,
                'coins' => $ApiConis ?? 0,
                'cashs' => $cashs ?? 0,
                'account'   => $user['coins'] ?? 0,
                'bank'  => $user['bank'] ?? 0,
                'agent_id'  => $v->agent_id,
                'type'  => 'sigle',
                'time'  => $yesterday

            ];
            daily::create($dailyUserSignle);
        }
    }
}
