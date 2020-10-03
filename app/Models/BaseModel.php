<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * 基类的模型 class
 */
class BaseModel extends Model
{




    #本地做用域处理显示 每个查询都进行限制
    public function scopeDirectly($query)
    {
        $currentManager = check_current_manager();
        if ($currentManager != false) {
            return $query->whereRaw(\DB::raw("agent_id IN ($currentManager)"));
        }
        return $query;
    }


    #本地做用域处理显示 每个查询都进行限制
    public function scopeDirectlyuid($query)
    {
        $currentManager = check_current_manager();
        if ($currentManager != false) {
            return $query->whereRaw(\DB::raw("uid IN ($currentManager)"));
        }
        return $query;
    }

}