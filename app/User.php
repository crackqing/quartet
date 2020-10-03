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


class User extends Authenticatable
{
    use HasApiTokens,Notifiable,EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','bind_id','mobile','agent_id','agent_nickname','status','manager'
    ];

    protected $appends = ['userProperty'];

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

    public function userRelation()
    {
        return  $this->hasMany('App\User', 'bind_id', 'email');
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


    public function getBindIdAttribute($value)
    {
        return $value == 0 ? '顶级管理' : $value;
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
    public function getUserPropertyAttribute()
    {
        if (isset(Auth::guard('api')->user()->email)){
            return  Auth::guard('api')->user()->email == $this->bind_id ?  '直属用户': '非直属用户';
            
        }else {
            return 0;
        }
    }

    #本地做用域处理显示 每个查询都进行限制
    public function scopeDirectly($query)
    {
        $currentManager = check_current_manager();
        if ($currentManager != false) {
            return $query->whereRaw(\DB::raw("agent_id IN ($currentManager)"));
        }
        return $query;
    }

    /**
     * 今日业绩--计算本天这个玩家的游玩记录  function
     *
     * @return void
     */
    public function todayPerformance()
    {
        $today = date('Y-m-d 00:00:00',time());
        $tomorrow = date('Y-m-d 00:00:00',strtotime('+1 day'));

        $record = RecordDetail::select(
            DB::raw('SUM(choushui) as choushui')
            )
            ->where('uid', $this->agent_id)
            ->whereBetween('time', [$today,$tomorrow])
            ->get();
        $choushui = 0;
        foreach ($record as $k => $v) {
            $choushui = $v->choushui;
        }            
        return $choushui * 20;
    }

    /**
     * 本周的业绩。按当前的自然来计算. function
     *
     * @return void
     */
    public function weekPerformance()
    {
        $carbon = Carbon::now();
        $startWeek = $carbon->startOfWeek()->format('Y-m-d H:i');
        $endWeek =$carbon->endOfWeek()->format('Y-m-d H:i');

        $record = RecordDetail::select(
            DB::raw('SUM(choushui) as choushui')
            )
            ->where('uid',$this->agent_id)
            ->whereBetween('time', [$startWeek,$endWeek])
            ->get();
        $choushui = 0;
        foreach ($record as $k => $v) {
            $choushui = $v->choushui;
        }
        return $choushui * 20;
    }
    /**
     * 上周1 至 周日 function
     *
     * @param [type] $id
     * @return void
     */
    public function lasgWeekPerformance()
    {
        $lastWeek = Carbon::parse('last week');
        $lastStartWeek = $lastWeek->startOfWeek()->format('Y-m-d H:i');
        $lastEndWeek =$lastWeek->endOfWeek()->format('Y-m-d H:i');

        $lastRecord = RecordDetail::select(
            DB::raw('SUM(choushui) as choushui')
            )
            ->where('uid',$this->agent_id)
            ->whereBetween('time', [$lastStartWeek,$lastEndWeek])
            ->get();
        $choushui = 0;
        foreach ($lastRecord as $k => $v) {
            $choushui = $v->choushui;
        }
        return $choushui * 20;
    }

}
