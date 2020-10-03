<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

use Zizaco\Entrust\Traits\EntrustUserTrait;
use Log,DB,Auth,Cache;
use App\Models\Game\Orders;
use App\Models\Game\Cashs;
use App\Models\Game\RecordDetail;
use App\Service\Tool;
use Illuminate\Support\Carbon;
use App\User;

class UserComplex extends Authenticatable
{
    use HasApiTokens,Notifiable,EntrustUserTrait;
    protected $table ='users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','bind_id','mobile','agent_id','agent_nickname','status','manager'
    ];

    protected $appends = ['directlyRecord','agentDirectlyData'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','amount'
    ];
    /**
     * 获取单个玩家每10分的记录操作 function
     *
     * @return void
     */
    public function recordSingle()
    {
        //外表的外建，与本地的local_id 对应上为 email
        return $this->hasMany('App\Models\Game\RecordSingle', 'uid', 'email');
    }
    /**
     * 获取单个玩家10内的详细记录，每一条的情况 function
     *
     * @return void
     */
    public function RecordDetail()
    {
        return $this->hasMany('App\Models\Game\RecordDetail', 'uid', 'email');
    }
    /**
     * 充值关联 function
     *
     * @return void
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Game\Orders', 'agent_id', 'email');
    }

    public function cashs()
    {
        return $this->hasMany('App\Models\Game\Cashs', 'agent_id', 'email');
    }

    /**
     * 赠送的情况 function
     *
     * @return void
     */
    public function apiConis()
    {
        return $this->hasMany('App\Models\Server\ApiConis', 'manager_id', 'id');
    }

    public function report()
    {
        return $this->hasMany('App\Models\Game\AgentReport', 'uid', 'email');
    }
    /**
     * 游玩记录 function
     *
     * @return void
     */
    public function userRecordDetail()
    {
        return $this->hasMany('App\Models\Game\RecordDetail','uid','agent_id');
    }
    /**
     * 当前的余额 function
     *
     * @param [type] $value
     * @return void
     */
    public function getBalanceAttribute($value)
    {
        return $value / 10000 ;
    }

    /**
     * 历史 ya de li not 直属 function
     *
     * @param string $time
     * @return void
     */
    public function getDirectlyRecordAttribute()
    {
        $yaReport = $this->report()->where('status', 3);

        $yaInt = $yaReport->sum('directly_ya');
        $deInt = $yaReport->sum('directly_de');
        $ylInt = $yaReport->sum('directly_yl');

        $notyaInt = $yaReport->sum('directly_not_ya');
        $notdeInt = $yaReport->sum('directly_not_de');
        $notylInt = $yaReport->sum('directly_not_yl');

        return ['ya' => $yaInt,
                'de' => $deInt,
                'yl' => $ylInt,
                'notya' => $notyaInt,
                'notde' => $notdeInt,
                'notyl' => $notylInt];
    }
    /**
     * @return string 直属中代理的ID 与数量  与 直属玩家数量 与ID
     */
    public function getAgentDirectlyDataAttribute()
    {
        if (isset($this->email)) {
            $user = Cache::remember('quartet_user_all', 60, function () {
                return User::select('id', 'email', 'bind_id', 'agent_id', 'manager')
                    ->get()
                    ->toArray();
            });
            $str = ''; $strAgent = ''; $strCount = 0; $strAgentCount = 0;
            if (isset($user)){
                foreach ($user as $k => $v) {
                    #代理ID
                    if ($v['manager'] == 1 && $v['bind_id'] == $this->email) {
                        $str .= $v['email'].',';

                        $strCount ++ ;
                    }
                    #直属玩家ID
                    if ($v['bind_id'] == $this->email){
                        $strAgent .= $v['email'].',';

                        $strAgentCount ++ ;
                    }
                }
                $agentDirectlyId = rtrim($str, ',');
                $agentNumberId = rtrim($strAgent, ',');

                #直属的总订单 与 总兑换  兑赠送 情况
                if (!empty($agentNumberId)){
                    $priceOrder = Orders::whereRaw(DB::raw("agent_id IN ($agentNumberId)"))
                            ->sum('price') / 100 ;

                    $priceCashs = Cashs::whereRaw(DB::raw("agent_id IN ($agentNumberId)"))
                            ->whereIn('status', [2,3,5])
                            ->sum('cash_money') / 100 ;
                    $apiCoins =  $this->apiConis()->whereRaw(DB::raw("uid IN ($agentNumberId)"))
                            ->whereIn('type', [10,6])
                            ->sum('coins') /10000;

                }

                $data = [
                    'agentDirectlyId' => $agentDirectlyId,
                    'agentDirectlyNumber' => $strCount,
                    'agentNumber'=> $strAgentCount, 
                    'agentNumberId' => $agentNumberId,
                    'directlyOrder' => $priceOrder ?? 0,
                    'directlyCashs' => $priceCashs ?? 0,
                    'directlyGive' => $apiCoins ?? 0

                ];

                return $data;
            }
        } else {
            $data = [
                'agentDirectlyId' => 0,
                'agentDirectlyNumber' =>0,
                'agentNumber'=> 0,
                'agentNumberId' => 0,
                'directlyOrder' => 0,
                'directlyCashs' => 0,
                'directlyGive' => 0
            ];
            return $data;
        }

    }

}
