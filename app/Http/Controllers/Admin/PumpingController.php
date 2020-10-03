<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Models\Game\DailyChoushui;
use App\Models\Game\DailyChoushuiAgent as dailyAgent;

use App\Models\Game\WeekChoushuiAgent;
use DB;

class PumpingController extends CommonController
{
    /**
     * datatables 动态表格处理 function
     *
     * @return void
     */
    public function index()
    {
        return view('Admin.pumping.index');
    }

    public function detail()
    {
        $daily = DailyChoushui::orderBy('time','DESC');

        $datatables = DataTables::of($daily);

        return $datatables->make(true);
    }

    /**
     * 个人业绩的显示  每日来实时算的。 所以团队需要另算了  function
     *
     * @return void
     */
    public function indexAgent()
    {
        return view('Admin.pumping.agent');
    }
    public function detailAgent()
    {
        $daily = dailyAgent::select(
                'id',
                'total_choushui',
                'time_rand',
                'bind_id',
                'time',
                'multiple',
                'return_gold'
            )
            ->orderBy('time','DESC')
            ->groupBy('bind_id','time');
        #增加对应的列显示
        return DataTables::of($daily)
                // ->addColumn('performance_add',function($daily){
                //     $level = agent_daily_percentage($daily->total_choushui);
                //     return $level[1] ;
                // })
                // ->addColumn('rreturn_gold_add',function($daily){
                //     $level = agent_daily_percentage($daily->total_choushui);

                //     return  floor( $level[1] * $daily->total_choushui ) ;
                // })
                ->addColumn('performance_add',function($daily){
                    $level = agent_daily($daily->total_choushui);
                     
                    return $level[0];
                })
                ->addColumn('rreturn_gold_add',function($daily){ //ceil
                    $level = agent_daily($daily->total_choushui);

                    return  floor($daily->total_choushui / 10000 ) * $level[1] ;
                })
                ->make(true);
    }

    /**
     * 树显示数据,方便运营查询与处理 function
     *
     * @return void
     */
    public function ztree()
    {
        $relationUser =  json_encode( user_relation_all(88888) ) ;

        return view('Admin.pumping.ztree',compact('relationUser'));
    }
    public function dailyWeek()
    {
        return view('Admin.pumping.week');
    }
    /**
     * 团队业绩--增加其它显示 function
     *
     * @return void
     */
    public function dailyWeekDetail()
    {

        $week = WeekChoushuiAgent::orderBy('time','DESC');
        //团队已领

        return DataTables::of($week)
                ->addColumn('self',function($week){
                    $daily = dailyAgent::where('time_rand',$week->time_rand)
                                ->where('bind_id',$week->uid)
                                ->first();
                    return $daily->total_choushui ?? 0;
                })
                ->make(true);
    }


}
