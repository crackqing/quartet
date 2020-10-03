<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

use Zizaco\Entrust\Traits\EntrustUserTrait;
use Log;
use Cache;
use DB;
use Auth;
use App\Models\Game\Orders;
use App\Models\Game\Cashs;
use App\Models\Game\RecordDetail;
use App\Service\Tool;
use App\Service\QpApi;
use App\Models\Game\AgentReport;
use App\User;

class UserSimple extends Model
{
    use HasApiTokens,Notifiable,EntrustUserTrait;

    protected $table ='users';

    protected $fillable = [
        'name', 'email', 'password','bind_id','mobile','agent_id','agent_nickname','status','manager'
    ];

    protected $hidden = [
        'password', 'remember_token','manager'
    ];

    protected $appends = ['agentDirectlyNumber','agentDirectlyId','agentNumber','agentNumberId','playerNumber','playerNumberId','directlyOrder','directlyCashs','directlyNotPlayerOrder','directlyNotPlayerCashs','totalBalance','directlyYa','directlyDe','directlyYl','directlyGive','directlyNotYa','directlyNotDe','directlyNotYl','directlyNotGive'];

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

    public function report()
    {
        return $this->hasMany('App\Models\Game\AgentReport', 'uid', 'email');
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

    public function userRelation()
    {
        return  $this->hasMany('App\UserSimple', 'bind_id', 'email');
    }
    /**
     * getAgentDirectlyNumberAttribute 这个才是直属的代理绑定而不是玩家... function
     *
     * @return void
     */
    public function getAgentDirectlyNumberAttribute()
    {
        if (isset($this->email)) {
            return count(user_relation_level($this->email));
        } else {
            return '';
        }
    }
    public function getAgentDirectlyIdAttribute()
    {
        if (isset($this->email)) {
            $relation =  user_relation_level($this->email) ;
            $str = '';
            foreach ($relation as $k => $v) {
                if (empty($v['email'])) {
                    continue ;
                }
                $str .= $v['email'].',';
            }
            return rtrim($str, ',') ;
        } else {
            return '';
        }
    }
    //|-------------------------------------------------直属玩家数量  兑换，充值，游戏数据
    /**
     * 直属玩家数量  function
     *
     * @return void
     */
    public function getAgentNumberAttribute()
    {
        return $this->userRelation()->count();
    }
    /**
     * 直属玩家数量 具体ID function
     *
     * @return void
     */
    public function getAgentNumberIdAttribute($manager_id = '')
    {
        #存在的情况，则取80001 后台ID
        if ($manager_id) {
            $relation = User::where('bind_id', $manager_id)->get();
        } else {
            $relation = $this->userRelation()->get();
        }
        $str = '';
        foreach ($relation as $k => $v) {
            if (empty($v->agent_id)) {
                continue ;
            }
            $str .= $v->agent_id.',';
        }
        #总押 ，总得，玩家盈利
        return rtrim($str, ',') ;
    }

    

    /**
     * 直属玩家数量 function
     *
     * @return void
     */
    public function getDirectlyOrderAttribute($time = false, $manager = false)
    {
        $str = $this->getAgentNumberIdAttribute($manager);
        if (!empty($str) && $time == false) {
            $price = Orders::whereRaw(DB::raw("agent_id IN ($str)"))
                ->sum('price') / 100 ;
        }
        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $price = Orders::whereRaw(DB::raw("agent_id IN ($str)"))
                ->whereBetween('created_at', [$time['yesterday'],$time['today']])
                ->sum('price') / 100;
        }
        
        return $price ?? 0;
    }



    /**
        * 直属玩家数量累计兑换 function
        *
        * @return void
        */
    public function getDirectlyCashsAttribute($time = false, $manager = false)
    {
        $str = $this->getAgentNumberIdAttribute($manager);
        if (!empty($str) && $time == false) {
            $price = Cashs::whereRaw(DB::raw("agent_id IN ($str)"))
                ->whereIn('status', [2,3,5])
                ->sum('cash_money') / 100 ;
        }

        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $price = Cashs::whereRaw(DB::raw("agent_id IN ($str)"))
                ->whereBetween('created_at', [$time['yesterday'],$time['today']])
                ->whereIn('status', [2,3,5])
                ->sum('cash_money') / 100;
        }
               

        
        return $price ?? 0;
    }

    /**
        * 直属玩家数量游戏记录，总押，总得，总盈利 function
        *
        * @return void
        */
    public function DirectlyRecordDetail($time = false, $manager =false)
    {
        $str = $this->getAgentNumberIdAttribute($manager);
        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $record = RecordDetail::select(
                    DB::raw('SUM(yazhu) as yazhu'),
                    DB::raw('SUM(defen) as defen'),
                    DB::raw('SUM(yingli) as yingli'),
                    DB::raw('SUM(choushui) as choushui')
                )
                    ->whereRaw(DB::raw("uid IN ($str)"))
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->get();
            return $record;
        }

        #查找 单个,可以会存在性能问题 先这样放上吧，考虑分区的情况 与elk
        if (!empty($str) && $time == false) {
            $record = RecordDetail::select(
                    DB::raw('SUM(yazhu) as yazhu'),
                    DB::raw('SUM(defen) as defen'),
                    DB::raw('SUM(yingli) as yingli'),
                    DB::raw('SUM(choushui) as choushui')

                )
                    ->whereRaw(DB::raw("uid IN ($str)"))
                    ->get();
                
            return $record;
        }
        return null;
    }

    //|-------------------------------------------------直属玩家数量计算 兑换，充值，游戏数据




    //|-------------------------------------------------非直属代理计算 兑换，充值，游戏数据

    /**
     * 非直属玩家数量 (也就是后台代理下面的玩家数量)) function  在单独写个代理数量
     *
     * @return void
     */
    public function getPlayerNumberAttribute()
    {
        if (isset($this->email)) {
            return count(user_relation($this->email));
        } else {
            return '';
        }
    }
    /**
     * 非直属玩家数量具体的ID显示  多层级显示 function
     *
     * @return void
     */
    public function getPlayerNumberIdAttribute($manager_id = '')
    {
        #获取的是对应的代理数量 然后继续获取代理下面的数量 递归处理
        // $relation = $this->userRelation()->where('manager', 1)->get();
        if ($manager_id) {
            $relation = user_relation((int)$manager_id);
        } else {
            $relation = user_relation($this->email);
        }
        $str = '';
        foreach ($relation as $k => $v) {
            if (empty($v['agent_id'])) {
                continue ;
            }
            $str .= $v['agent_id'].',';
        }
        return rtrim($str, ',') ;
    }

    /**
     * 非直属玩家累计充值 function
     *
     * @return void
     */
    public function getDirectlyNotPlayerOrderAttribute($time = false, $manager_id = '')
    {
        #返回的manager_id为空
        $str = $this->getPlayerNumberIdAttribute($manager_id);
        if (!empty($str) && $time == false) {
            $price = Orders::whereRaw(DB::raw("agent_id IN ($str)"))
                ->sum('price') / 100 ;
        }
        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $price = Orders::whereRaw(DB::raw("agent_id IN ($str)"))
                ->whereBetween('created_at', [$time['yesterday'],$time['today']])
                ->sum('price') / 100;
        }
        return $price ?? 0;
    }


    /**
        * 非直属玩家累计兑换 function
        *
        * @return void
        */
    public function getDirectlyNotPlayerCashsAttribute($time = false, $manager = false)
    {
        $str = $this->getPlayerNumberIdAttribute($manager);
        if (!empty($str) && $time == false) {
            $price = Cashs::whereRaw(DB::raw("agent_id IN ($str)"))
                    ->whereIn('status', [2,3,5])
                    ->sum('cash_money') / 100 ;
        }
    
        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $price = Cashs::whereRaw(DB::raw("agent_id IN ($str)"))
                    ->whereBetween('created_at', [$time['yesterday'],$time['today']])
                    ->whereIn('status', [2,3,5])
                    ->sum('cash_money') / 100;
        }
                   
        return $price ?? 0;
    }


    /**
    * 直属玩家游戏记录，总押，总得，总盈利 function
    *
    * @return void
    */
    public function DirectlyNotRecordDetail($time = false, $manager_id)
    {
        $str = $this->getPlayerNumberIdAttribute($manager_id);
        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $record = RecordDetail::select(
                        DB::raw('SUM(yazhu) as yazhu'),
                        DB::raw('SUM(defen) as defen'),
                        DB::raw('SUM(yingli) as yingli')
                    )
                        ->whereRaw(DB::raw("uid IN ($str)"))
                        ->whereBetween('time', [$time['yesterday'],$time['today']])
                        ->get();
            return $record;
        }
        return null;
    }


    //|-------------------------------------------------非直属代理计算 兑换，充值，游戏数据




    /**
     * 游戏总余额 (缓存4小时 自然时间) 直属的余额 function
     *
     * @return void
     */
    public function getTotalBalanceAttribute()
    {
        return 0;
    }
    //->whereBetween('time', [$time['yesterday'],$time['today']])
    /**
     * status  1为充值，2为兑换，3为游戏记录 4为计算单个游戏的情况function
     *
     * @param string $time
     * @return void
     */
    public function getDirectlyYaAttribute($time ='')
    {
        if ($time) {
            $ya = $this->report()->where('status', 3)
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->sum('directly_ya') / 10000;
        } else {
            $ya = $this->report()->where('status', 3)
                    ->sum('directly_ya') ;
        }
        return $ya ?? 0;
    }
    public function getDirectlyDeAttribute($time ='')
    {
        if ($time) {
            $de = $this->report()->where('status', 3)
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->sum('directly_de') / 10000;
        # code...
        } else {
            $de = $this->report()->where('status', 3)
                    ->sum('directly_de') ;
        }

        return $de ?? 0;
    }
    public function getDirectlyYlAttribute($time ='')
    {
        if ($time) {
            $yl = $this->report()->where('status', 3)
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->sum('directly_yl') / 10000 ;
        # code...
        } else {
            $yl = $this->report()->where('status', 3)
                    ->sum('directly_yl') ;
        }
        return $yl ?? 0;
    }

    public function getDirectlyNotYaAttribute($time ='')
    {
        if ($time) {
            $ya = $this->report()->where('status', 3)
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->sum('directly_not_ya') / 10000 ;
        # code...
        } else {
            $ya = $this->report()->where('status', 3)
                    ->sum('directly_not_ya') ;
        }
        return $ya ?? 0;
    }
    public function getDirectlyNotDeAttribute($time ='')
    {
        if ($time) {
            $de = $this->report()->where('status', 3)
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->sum('directly_not_de') / 10000;
        # code...
        } else {
            $de = $this->report()->where('status', 3)
                    ->sum('directly_not_de') ;
        }
        return $de ?? 0;
    }
    public function getDirectlyNotYlAttribute($time ='')
    {
        if ($time) {
            $yl = $this->report()->where('status', 3)
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->sum('directly_not_yl') / 10000;
        # code...
        } else {
            $yl = $this->report()->where('status', 3)
                    ->sum('directly_not_yl') ;
        }
        return $yl ?? 0;
    }
    /**
     * 直属赠送的数量 qt_api_conis type 10  function
     *
     * @param [type] $time
     * @return void
     */
    public function getDirectlyGiveAttribute($time = '')
    {
        #获取直属玩家 在进行统计 whereRaw(DB::raw("agent_id IN ($str)"))
        $str = $this->getAgentNumberIdAttribute();
        if (empty($str)) {
            return 0;
        }
        $apiCoins =  $this->apiConis()->whereIn('type', [10,6])
                ->whereRaw(DB::raw("uid IN ($str)"))
                ->sum('coins') /10000;
        return $apiCoins ?? 0;
    }
    /**
     * 非直属的充值 function
     *
     * @param string $time
     * @return void
     */
    public function getDirectlyNotGiveAttribute($time = '')
    {
        $str = $this->getPlayerNumberIdAttribute();
        if (empty($str)) {
            return 0;
        }
        $apiCoins =  $this->apiConis()->whereIn('type', [10,6])
                ->whereRaw(DB::raw("uid IN ($str)"))
                ->sum('coins') /10000;
        return $apiCoins ?? 0;
    }



    /**
     * 非直属的总得 总押 与 总盈利
     */

    public function getDirectlyNotYa($time ='')
    {
        if ($time) {
            $ya = $this->report()->whereBetween('time', [$time['yesterday'],$time['today']])->sum('directly_not_ya') / 10000;
        # code...
        } else {
            $ya = $this->report()->sum('directly_not_ya') / 10000;
        }
        return $ya ?? 0;
    }
    public function getDirectlyNotDe($time ='')
    {
        if ($time) {
            $de = $this->report()->whereBetween('time', [$time['yesterday'],$time['today']])->sum('directly_not_de') / 10000;
        # code...
        } else {
            $de = $this->report()->sum('directly_not_de') / 10000;
        }
        return $de ?? 0;
    }
    public function getDirectlyYlNot($time ='')
    {
        if ($time) {
            $yl = $this->report()->whereBetween('time', [$time['yesterday'],$time['today']])->sum('directly_not_yl') / 10000;
        # code...
        } else {
            $yl = $this->report()->sum('directly_not_yl') / 10000;
        }

        return $yl ?? 0;
    }


    public function getBindIdAttribute($value)
    {
        return $value == 0 ? '顶级管理' : $value;
    }
    /**
     * 累计充值 (总合) function
     *
     * @return void
     */
    public function getOrderTotalAttribute()
    {
        return $this->orders()->sum('price');
    }

    public function getMobileAttribute($value)
    {
        return isset($value) ? $value : '尚未绑定';
    }

    /*             单个用户的总充值，总赠送 总押与总得                   */
    /**
    * 直属玩家数量游戏记录，总押，总得，总盈利 function
    *
    * @return void
    */
    public function DirectlyRecordDetailKindid($time = false, $manager =false)
    {
        $str = $this->getAgentNumberIdAttribute($manager);
        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $record = RecordDetail::select(
                        DB::raw('SUM(yazhu) as yazhu'),
                        DB::raw('SUM(defen) as defen'),
                        DB::raw('SUM(yingli) as yingli'),
                        DB::raw('SUM(choushui) as choushui'),
                        'kindid'
                    )
                    ->whereRaw(DB::raw("uid IN ($str)"))
                    ->groupBy('kindid')
                    ->whereBetween('time', [$time['yesterday'],$time['today']])
                    ->get();
            return $record;
        }
        return null;
    }

    public function DirectlyNotRecordDetailKindid($time = false, $manager_id)
    {
        $str = $this->getPlayerNumberIdAttribute($manager_id);
        #存在时间的情况下则是单条统计
        if (!empty($str) && $time) {
            $record = RecordDetail::select(
                            DB::raw('SUM(yazhu) as yazhu'),
                            DB::raw('SUM(defen) as defen'),
                            DB::raw('SUM(yingli) as yingli'),
                            'kindid'
                        )
                        ->whereRaw(DB::raw("uid IN ($str)"))
                        ->groupBy('kindid')
                        ->whereBetween('time', [$time['yesterday'],$time['today']])
                        ->get();
            return $record;
        }
        return null;
    }



    /*             单个用户的总充值，总赠送 总押与总得                   */
}
