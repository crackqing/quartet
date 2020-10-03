<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use DB,Config,Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp\Client;


use App\Models\Game\RecordDetailIntval;
use App\Models\Game\Orders;
use App\Models\Game\WeekChoushuiAgent;

use App\UserSimple;
use App\User;

use App\Models\Game\DailyChoushuiAgent as dailyAgent;
use App\Models\Game\DailyChoushui as dailyUid;

use App\Http\Resources\Client\PersonalCollection;
/**
 * 代理后台密码登录到游戏界面展示的前提
 *  1.需要绑定关联特定的游戏ID
 *  2.游戏通过玩家ID进行登录(后台提供的代理密码,而不是游戏)--令牌发放
 */
class ClientController extends BaseController
{
    use AuthenticatesUsers;

    public $yesterdayTime ;
    public $yesterdayEnd ;

    public $today ;
    public $todayEnd ;

    public function __construct()
    {
        $this->yesterdayTime = Carbon::parse('yesterday')->format('Y-m-d 00:00:00');
        $this->yesterdayEnd = Carbon::parse('yesterday')->format('Y-m-d 23:59:59');

        $this->today = Carbon::parse('today')->format('Y-m-d 00:00:00');
        $this->todayEnd = Carbon::parse('today')->format('Y-m-d 23:59:59');
    }
    /**
     * 登录检测---是否绑定后台的代理帐号 5位 function
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $email = $request->agent_id;
        $password = $request->password;
        $user = User::where(['agent_id' => $email,'manager' => 1])->first();

        if (!$user) {
            return $this->nativeRespond('5001',['message'=>'登录密码错误或帐号错误'],'success');
        }
        if($user->status == 1 ){
            return  $this->nativeRespond('5002',['message'=> '代理帐号已被封,请联系管理员处理'],'success');
        } 
        #用的是passport 第三方库 授权模式 为密码  完善的oauth验证
        if ($this->guard('client')->attempt(['email' => $user->email,'password' => $password],'remember')) {
            $http = new Client();
            $url = Config::get('services.oauth_api'); 
            $response = $http->post($url,[
                'form_params'   => [
                    'grant_type'    => 'password',
                    'client_id' => '2',
                    'client_secret' => '7pWbYdhhfJKLdREFIKFqCOohsDqxSBRURUyvsqc0',
                    'username'  => $user->email,
                    'password'  => $password,
                    //使用密码授权的时候，你可能想要对应用所支持的所有域进行令牌授权，这可以通过请求 * 域来实现。如果你请求的是 * 域，则令牌实例上的 can 方法总是返回 true
                    'scope' => '*'                
                ]
            ]);
            return json_decode((string)$response->getBody(), true);    
        }
        return $this->nativeRespond('5001',['message'=>'登录密码错误或帐号错误'],'success');
    }
    /**
     * 个人数据  实时检测代理用户状态，被封则直接T出 function
     *
     * @return void
     */
    public function personal()
    {
        $directly = $this->directlyPlayerId();

        $todayPersonalResults = 0;
        $todayPersonalRecharge = 0;
        #个人今日业绩
        if (!empty($directly)) {
            $RecordDetailIntval = RecordDetailIntval::whereRaw(DB::raw("uid IN ($directly)"))
                                ->whereBetween('time', [$this->today,$this->todayEnd])
                                ->get();
            foreach ($RecordDetailIntval as $k => $v) {
                $todayPersonalResults  += $v->yingli ;
            }
            $todayPersonalRecharge = Orders::select('created_at', 'agent_id', 'price')
                        ->whereRaw(DB::raw("agent_id IN ($directly)"))
                        ->whereBetween('created_at', [$this->today,$this->todayEnd])
                        ->sum('price');
        }
        #佣金信息单个提取 个业已领佣金，   团队总佣金     团队已领佣金   差额团队佣金 今日实发
        $yesterdayCommission =  WeekChoushuiAgent::where('time_rand', $this->yesterdayTime.' - '.$this->yesterdayEnd)
        ->where('uid', $this->clientLoginAgent())
        ->first();

        $data = [
            'todayPersonalResults'  => $todayPersonalResults,
            'todayPersonalRecharge'  => $todayPersonalRecharge,
            'yesterdayPersonalCommission'  => $yesterdayCommission->receive ?? 0,
            'yesterTeamCommission'  => $yesterdayCommission->return_gold ?? 0,
        ];
        return $this->success($data);
    }
    public function team()
    {
        $directylId = $this->directlyPlayerId();

        $yesterdayPersonalResult = dailyAgent::where('bind_id', $this->clientLoginAgent())
                            ->whereBetween('time_rand', [$this->yesterdayTime,$this->yesterdayEnd])
                            ->first();
        $yesterdayTeamCommission = WeekChoushuiAgent::where('uid', $this->clientLoginAgent())
                            ->whereBetween('time_rand', [$this->yesterdayTime,$this->yesterdayEnd])
                            ->first();
        $personalPlayersNew = User::where('bind_id', $this->clientLoginAgent())
                            ->whereBetween('created_at', [$this->yesterdayTime,$this->yesterdayEnd])
                            ->count();
        $personalPlayersYesterdayRecharge = Orders::select('created_at', 'agent_id', 'price')
                            ->whereRaw(DB::raw("agent_id IN ($directylId)"))
                            ->whereBetween('created_at', [$this->yesterdayTime,$this->yesterdayEnd])
                            ->sum('price');
        //'teamPlayerNum' => $this->personalNumber() + $this->teamNumber()
        $data = [
            'yesterdayPersonalResult' => $yesterdayPersonalResult->total_choushui ?? 0,
            'personalPlayersNum' => $this->personalNumber(),
            'yesterdayTeamCommission' => $yesterdayTeamCommission->total_choushui ?? 0,
            'teamPlayerNum' => 0,
            'personalPlayersNew' => $personalPlayersNew,
            'personalPlayersYesterdayRecharge' => $personalPlayersYesterdayRecharge / 100 ?? 0,
        ];
        return $this->success($data);
    }

    /**
     * 玩家数量 对应就是直属总数
     *
     * @return void
     */
    public function personalNumber()
    {
        return $this->userSimpleModels()->getAgentNumberAttribute() ?? 0;
    }

    /**
     * 直属绑定代理ID，当前用户的登录情况 function
     *
     * @return void
     */
    public function directlyPlayerId()
    {
        return $this->userSimpleModels()->getAgentNumberIdAttribute();
    }
    /**
     * 团队数量 为 直属 +非直属 数量 player为非直属 function
     *
     * @return void
     */
    public function teamNumber()
    {
        return $this->userSimpleModels()->getPlayerNumberAttribute();
    }



    public function directlyAgentId()
    {
        return $this->userSimpleModels()->getAgentDirectlyIdAttribute();
    }


    /**
     * 个个业绩---直属代理ID (实时流水 今日) unction
     *
     * @return void
     */
    public function personalCurrentWater(Request $request)
    {
        $paginate = $request->paginate ?? 20;
        $directly = $this->directlyPlayerId();

        $record = RecordDetailIntval::select(
                'time',
                'uid',
                'kindid',
                'yingli'
            )
            ->whereRaw(DB::raw("uid IN ($directly)"))
            ->whereBetween('time', [$this->today,$this->todayEnd])
            ->orderBy('time', 'DESC');
        ;
        return new PersonalCollection($record->paginate($paginate));
    }
    /**
     * 昨日个人业绩详情 function
     *
     * @return void
     */
    public function yesterdayPersonalCommission(Request $request)
    {
        $paginate = $request->paginate ?? 20;
        $directly = $this->directlyPlayerId();

        $dailyUid = dailyUid::select('uid', 'total_choushui', 'time')
                        ->whereRaw(DB::raw("uid IN ($directly)"))
                        ->whereBetween('time', [$this->yesterdayTime,$this->yesterdayEnd])
                        ->orderBy('time', 'DESC');

        return new PersonalCollection($dailyUid->paginate($paginate));
    }
    /**
     * 昨日团队业绩详情--下级业绩(直属的绑定ID) function
     *
     * @param Request $request
     * @return void
     */
    public function yesterTeamResults(Request $request)
    {
        $paginate = $request->paginate ?? 20;
        $directlyAgentId = $this->directlyAgentId();

        $dailyAgent = dailyAgent::select('bind_id', 'time', 'return_gold')
                            ->whereRaw(DB::raw("bind_id IN ($directlyAgentId)"))
                            ->whereBetween('time', [$this->yesterdayTime,$this->yesterdayEnd])
                            ->orderBy('time', 'DESC');
        return new PersonalCollection($dailyAgent->paginate($paginate));
    }
    /**
     * 昨日团队佣金详情 --佣金信息--都是直属的代理 与不是 直属玩家function
     *
     * @return void
     */
    public function yesterdayTeamCommission(Request $request)
    {
        $paginate = $request->paginate ?? 20;
        $directlyAgentId = $this->directlyAgentId();

        $week = WeekChoushuiAgent::whereRaw(DB::raw("uid IN ($directlyAgentId)"))
                                ->where('total_choushui', '!=', 0)
                                ->whereBetween('time', [$this->yesterdayTime,$this->yesterdayEnd])
                                ->orderBy('time', 'DESC');
        return new PersonalCollection($week->paginate($paginate));
    }
    /**
     * 当前游戏客户端登录的代理模型---各类方法处理 function
     *
     * @return void
     */
    public function userSimpleModels()
    {
        return UserSimple::find(Auth::guard('client')->user()->id);
    }
    /**
     * 登录的ID id ==> email ==> agent_id  function
     *
     * @return void
     */
    public function clientLoginAgent()
    {
        return Auth::guard('client')->user()->email;
    }






}
