<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class ApiConis extends BaseModel
{
    protected $primaryKey = 'id';

    protected $table ='qt_api_conis';

    protected $guarded = [];

    protected $hidden = [
        'updated_at'
    ];

    protected $appends = ['give_total','ipCount'];


    public function user()
    {
        return $this->belongsTo('App\User','uid','email')->withDefault();
    }

    public function manager()
    {
        return $this->belongsTo('App\User','manager_id','id')->withDefault();
    }

    public function apiCoinsRelation()
    {
        return $this->hasMany('App\Models\Server\ApiConis','uid','uid');
    }

    public function getGiveTotalAttribute()
    {
        return $this->apiCoinsRelation()->where('uid',$this->uid)->sum('coins') / 10000;
    }

    public function getIpCountAttribute()
    {
        return $this->where('ip',$this->ip)->count();
    }


    public function getBalanceAttribute($value)
    {
        return $value / 10000;
    }

    public function getCoinsAttribute($value)
    {
        return $value / 10000 ;
    }

    public function getBankAttribute($value)
    {
        return $value / 10000 ;
    }

    public function getBeforeCoinsAttribute($value)
    {
        return $value / 10000;
    }

    //赠送代理是 user manager 为1  不为1的是用户

    public function userManger()
    {
        return $this->user()->where('manager',1);
    }

    public function userNormal()
    {
        return $this->user()->where('manager',0);

    }

}
