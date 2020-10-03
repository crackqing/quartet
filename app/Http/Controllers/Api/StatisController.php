<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use App\Models\Game\Higcharts;

class StatisController extends BaseController
{
    /**
     * 新增 与日活跃玩家，默认返回最新30数据 function
     *
     * @return void
     */
    public function new(Request $request)
    {
        $time = $request->time ?? false;
        $higcharts = Higcharts::select(
                'id',
                'date',
                'new as 新增玩家',
                'login as 日活跃玩家',
                'bind as 日绑定玩家'
        )
        ->when($time,function($query) use ($time){
            $time = explode(' - ', $time);

            return $query->whereBetween('created_at', [$time[0],$time[1]]);
        })
        ->orderBy('id', 'DESC')
        ->groupBy('date')
        ->limit(30)
        ->get();
        return  $this->success($higcharts);
    }
    /**
     * 日活，留存，3日，七日 function
     *
     * @return void
     */
    public function remain(Request $request)
    {
        $time = $request->time ?? false;
        $higcharts = Higcharts::select(
            'id',
            'date',
             'remain_1 as 次日留存',
             'remain_3 as 3日留存',
             'remain_7 as 7日留存'
            )
            ->when($time,function($query) use ($time){
                $time = explode(' - ', $time);

                return $query->whereBetween('created_at', [$time[0],$time[1]]);
            })
            ->orderBy('id', 'DESC')
            ->groupBy('date')
            ->limit(30)
            ->get()
            ->toArray();
        foreach ($higcharts as $k => $v) {
            $higcharts[$k]['次日留存'] = round($v['次日留存'] / 100, 2);
            $higcharts[$k]['3日留存'] = round($v['3日留存'] / 100, 2);
            $higcharts[$k]['7日留存'] = round($v['7日留存'] / 100, 2);
        }
        return  $this->success($higcharts);
    }
}
