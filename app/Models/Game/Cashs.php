<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use Config;
use App\Models\BaseModel;
class Cashs extends BaseModel
{
    // protected $connection = 'tencent_mysql';
    protected $table ='qt_cashs';

    protected $guarded = [];
    protected $appends = ['count','totalAmonunt'];

    protected $hidden = [
        'updated_at','exchangeid','order_id'
    ];

    public function manager()
    {
        return $this->belongsTo('App\User','manager_id','id')->withDefault('');
    }

    public function paraent()
    {
        return $this->belongsTo('App\User','agent_id','agent_id')->withDefault('');
    }

    /**
     * 兑换次数显示 function
     *
     * @return void
     */
    public function getCountAttribute()
    {
        return $this->cashsCount()->count();
    }

    public function getTotalAmonuntAttribute()
    {
        return $this->cashsCount()->whereIn('status',[2,3,5])->sum('cash_money') / 100 .'元';
    }


    public function getCoinsAttribute($value)
    {
        return $value / 10000;
    }

    public function getBankAttribute($value)
    {
        return $value / 10000;
    }

    public function getCashMoneyAttribute($value)
    {
        return $value / 100 ;
    }



    /**
     * 自定义次数字段与总金额处理。而不是在查询上操作  function
     *
     * @return void
     */
    public function cashsCount()
    {
        return $this->hasMany('App\Models\Game\Cashs','agent_id','agent_id');
    }
    /**
     * 关联对应的操作人 function
     *
     * @return void
     */
    // public function userRelation()
    // {

    // }



}
