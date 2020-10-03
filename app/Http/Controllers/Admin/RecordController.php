<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\Game\RecordDetail;
use App\Models\Game\RecordDetailIntval;
use App\User;

use Illuminate\Support\Carbon;
use DB;
use Config;
use App\Traits\Csv;

use Yajra\DataTables\DataTables;
use App\Models\Game\Orders;

class RecordController extends CommonController
{
    /**
     * 游戏记录 100页 function
     *
     * @return void
     */
    public function index()
    {
        return view('Admin.record.index');
    }
    /**
     * 游戏记录--datatables- function
     *
     * @return void
     */
    public function indexDetail()
    {
        $record =  RecordDetail::orderBy('id','DESC');

        $datatables = DataTables::of($record)
            ->filter(function($query){
            #时间区间
            if (request()->has('time')) {
                if (!empty(request('time'))) {
                    $time = explode(' - ', request('time')) ;

                    $query->whereBetween('time',[$time[0],$time[1]]);
                }
            }
        },true);
        return $datatables->make(true);
    }
    /**
     * 导出表格处理 function
     *
     * @return void
     */
    public function recordCsv(Request $request)
    {
        $record =  RecordDetailIntval::orderBy('id','desc')->limit(40000)->get()->toArray();
        foreach ($record as $k => $v) {
            $user= User::where('agent_id',$v['uid'])->value('bind_id');

            $record[$k]['bind_id'] = $user ?? 0;
        }
        $header = ['序列','游玩时间','用户ID','游戏类别','押注','得分','赢利','抽水','金币','银行','桌子TID','桌子名称','是否庄家','下注类型','当前期数','下注牌型','开奖牌型','绑定ID'];

        $fileName = Config::get('app.name').'游玩记录'.date('Y_m_d').time();

        Csv::csvFile2($header,20000,$fileName,$record);
    }

    /**
     * 带上级ID的处理方式 function
     *
     * @return void
     */
    public function orderCsv()
    {
        $order = Orders::select('id','price','order_id','agent_id','pay_type','created_at','remark')
                            ->orderBy('id','DESC')
                            ->limit(50000)
                            ->get()
                            ->toArray();
        foreach ($order as $k => $v) {
            $user= User::where('agent_id',$v['agent_id'])->value('bind_id');
            $order[$k]['bind_id'] = $user ?? 0;
        }
        $header = ['序列','订单价格','订单号','代理ID','支付类型','支付时间','备注','绑定ID'];
        $fileName = Config::get('app.name').'订单记录'.date('Y_m_d').time();
        Csv::csvFile2($header,20000,$fileName,$order);
    }
}
