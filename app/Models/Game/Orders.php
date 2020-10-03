<?php

namespace App\Models\Game;

use App\Models\BaseModel;


class Orders extends BaseModel
{

    // protected $connection = 'tencent_mysql';

    
    protected $table ='qt_orders';

    // protected $fillable $guarded
    protected $guarded = [];

    protected $appends = ['order_total'];
    

    public function user()
    {
        return $this->belongsTo('App\User','agent_id','email')->withDefault();
    }


    public function manager()
    {
        return $this->belongsTo('App\User','manager_id','id')->withDefault();
    }
    
    public function orderRelation()
    {
        return $this->hasMany('App\Models\Game\Orders','agent_id','agent_id');
    }

    public function getOrderTotalAttribute()
    {
        return $this->orderRelation()->where('agent_id',$this->agent_id)->sum('price') / 100;
    }


    public function getCoinsAttribute($value)
    {
        return $value / 10000;
    }

    public function getBankAttribute($value)
    {
        return $value / 10000;
    }

    public function getBeforeCoinsAttribute($value)
    {
        return $value / 10000;
    }

    public function getPriceAttribute($value)
    {
        return $value / 100 ;
    }

    public function getPayTypeAttribute($value)
    {
        switch ($value) {
            case 'zfb':
            return '支付宝';
                break;
            case 'wx':
                return '微信';
                break;
            case 'unionpay':
                return '银联';
                break;
            case 'qq':
                return 'QQ支付';
                break;
            default:
               return $value;
                break;
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

}
