<?php
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Front\CommonController;
use App\User;
use App\Models\Game\RecordDetail;
use App\Models\Game\RecordDetailIntval;
use App\Models\Game\DailyChoushui as dailyUid;
use App\Models\Game\DailyChoushuiAgent as dailyAgent;
use App\Models\Game\WeekChoushuiAgent;

use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use DB,Config;
class AgentsController extends CommonController
{
    /**
     *面板设置 function
     *
     * @return void
     */
    public function dashboard()
    {
        $this->qrcodeGeneric();

        $directly = $this->GetAgentNumberId();
        $yesterdayTime = Carbon::parse('yesterday')->format('Y-m-d H:i:00');
        $yesterdayEnd =  Carbon::parse('yesterday')->format('Y-m-d 23:59:59');

        $today = date('Y-m-d 00:00:00',time());
        $todayEnd = date('Y-m-d 23:59:59',time());

        #不存在则生成二维码,并设置图片的位置
        if (Config::get('app.name') == 'tiantian') {
            $sharePng = '/qrcode/'.$this->authIdEmail().'_share.png';
        } else {
            $sharePng = '/qrcode/'.$this->authIdEmail().'.png';
        }

        $userSimple = $this->getUserSimple();
        $getAgentDirectly = $userSimple->getAgentNumberAttribute();
        $getAgentNumberIdAttribute = $userSimple->getPlayerNumberAttribute();
        #昨日业绩的个人与团队  今天的统计所以取今天时间就行
        $week = WeekChoushuiAgent::where('uid',$this->authIdEmail())
                                ->whereBetween('time_rand', [$yesterdayTime,$yesterdayEnd])
                                ->first();
        $yesterDay = dailyAgent::where('bind_id',$this->authIdEmail())
                                ->whereBetween('time_rand', [$yesterdayTime,$yesterdayEnd])
                                ->first();
        $RecordDetailIntvalYingli = 0;
        #个人今日业绩
        if (!empty($directly)) {
            $RecordDetailIntval = RecordDetailIntval::whereBetween('time', [$today,$todayEnd])
                                    ->whereRaw(DB::raw("uid IN ($directly)"))
                                    ->get();
            foreach($RecordDetailIntval as $k => $v){
                $RecordDetailIntvalYingli  += $v->yingli ;
            }
        } 
        #佣金信息单个提取 个业已领佣金，   团队总佣金     团队已领佣金   差额团队佣金 今日实发
        $weekSelfId =  WeekChoushuiAgent::where('time_rand',$yesterdayTime.' - '.$yesterdayEnd)
                                        ->where('uid',$this->authIdEmail())
                                        ->first();
        return view('Front.Agent.dashboard',compact('sharePng','getAgentDirectly','getAgentNumberIdAttribute','week','yesterDay','RecordDetailIntvalYingli','weekSelfId'));
    }
    /**
     *直属玩家列表  function
     *
     * @return void
     */
    public function index()
    {
        return view('Front.Agent.index');
    }
    /**
     * 列出当前玩家的详细 function
     * 
     * @return void
     */
    public function detail()
    {
        $directly = $this->GetAgentNumberId();
        $dailyUid = dailyUid::select('uid','total_choushui','time')
                        ->whereRaw(DB::raw("uid IN ($directly)"));
                        
        $datatables = DataTables::of($dailyUid);
        return $datatables->make(true);
    }
    /**
     * 详细的，本周时间内 游戏种类的 抽水详细 groupBy function
     *
     * @return void
     */
    public function detailShowModal($agent_id)
    {
        $_token = csrf_token();
        $carbon = Carbon::now();
        $startWeek = $carbon->startOfWeek()->format('Y-m-d H:i');
        $endWeek =$carbon->endOfWeek()->format('Y-m-d H:i');

        $sigleUser = User::where('agent_id', $agent_id)->first();

        $record = RecordDetail::select(
            DB::raw('SUM(choushui) as choushui'),
            'kindid'
            )
            ->where('uid',$agent_id)
            ->whereBetween('time', [$startWeek,$endWeek])
            ->groupBy('kindid')
            ->get();
        $str ='<tr>';
        foreach ($record as $k => $v) {
            $gameType = kindid2game($v->kindid);

            $str .= "<td> 时间区间为: {$startWeek} - {$endWeek}"  .  "   游戏类为: {$gameType}"  .  "  业绩为 {$v->choushui} </td>";
        }
        $str .= '</tr>';

        $modal = <<<STR
<div class="modal  fade" id="myModal{$agent_id}" tabindex="-1" role="dialog" aria-labelledby="myModal{$agent_id}">
    <div class="modal-dialog" role="document">
     <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModal{$agent_id}">业绩具体详细</h4>
          </div>

           <div class="modal-body">
                <table class="table">
                        <thead>
                            <tr>
                                <th>时间区间--游戏种类--业绩金额</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$str}
                        </tbody>
                </table>
                <p class="text-danger">当前用户ID为:{$agent_id}<p>
         </div>
          <div class="modal-footer">


          </div>

     </div>

    </div>  
</div>

STR;
        return $modal;
    }
    public function datatableZh()
    {
        return $this->datatablesChinese();
    }
    #面板---个人业绩详细  与玩家列表 下级业绩  佣金信息 FAQ

    /**
     * 个人业绩详细  function
     *
     * @return void
     */
    public function dbDetailed()
    {
        return view('Front.Agent.dbdetailed');
    }
    public function Detaild()
    {
        $today = Carbon::now()->format('Y-m-d 23:59:59');
        $todayEnd = Carbon::now()->format('Y-m-d 00:00:00'); //默认显示20天的数据

        $directly = $this->GetAgentNumberId();
        $RecordDetailIntval = RecordDetailIntval::select(
                        'time','uid','kindid','yingli'
                    )   
                    ->whereBetween('time', [$todayEnd,$today])
                    ->whereRaw(DB::raw("uid IN ($directly)"));
        return  DataTables::of($RecordDetailIntval)
                    ->editColumn('yingli', function($RecordDetailIntval) {
                            return $RecordDetailIntval->yingli  ;
                    })
                    ->editColumn('kindid', function($RecordDetailIntval){
                        return kindid2game($RecordDetailIntval->kindid);
                    })
                    ->make(true);
    }
    /**
     * 下级业绩 非直属玩家的记录 function
     *
     * @return void
     */
    public function nonDirect()
    {
        return view('Front.Agent.nonDirect');
    }
    /**
     * 直属玩家的显示 function
     *
     * @return void
     */
    public function nonDirectDetail()
    {
        //直属代理ID,而不是直属玩家的显示
        $getAgentDirectly = $this->getAgentDirectly();

        $dailyAgent = dailyAgent::select('bind_id','time','return_gold','total_choushui')
                                ->whereRaw(DB::raw("bind_id IN ($getAgentDirectly)"));

        return DataTables::of($dailyAgent)
                    ->editColumn('uid',function($dailyAgent){
                        $bind = User::where('email',$dailyAgent->bind_id)->value('bind_id');
                        return $bind ?? '未知';
                    })
                    ->editColumn('return_gold',function($dailyAgent){
                        $level = agent_daily($dailyAgent->total_choushui);

                        return  floor($dailyAgent->total_choushui / 10000 ) * $level[1] ;
                    })
                    ->make(true);
    }
    /**
     * 佣金信息 sf_week_choushui_agent function
     *
     * @return void
     */
    public function commission()
    {
        return view('Front.Agent.commission');
    }
    public function commissionDetail()
    {
        $getAgentDirectly = $this->getAgentDirectly();
        $week = WeekChoushuiAgent::whereRaw(DB::raw("uid IN ($getAgentDirectly)"))
                                ->where('total_choushui','!=',0)
                                ->orderBy('time','DESC');

        return DataTables::of($week)
                ->editColumn('uid',function($week){
                    if ($this->authIdEmail() == $week->uid) {
                        return '<p class="bg-danger">'.$week->uid.'</p>';
                    }
                    return $week->uid;
                })
                ->addColumn('paraent_id',function($week){
                    $bind = User::where('email',$week->uid)->value('bind_id');
                    return $bind ?? '未知';
                })
                ->addColumn('daily_agent',function($week){
                    $total_choushui = dailyAgent::where('bind_id',$week->uid)
                        ->where('time_rand',$week->time_rand)
                        ->value('total_choushui');
                    return $total_choushui ?? 0;
                })
                ->addColumn('today_pay',function($week){

                    $today_pay = $week->payable + $week->receive;
                    return $today_pay ?? 0;
                })
                ->escapeColumns([])
                ->make(true);
    }


}
