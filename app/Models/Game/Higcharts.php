<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class Higcharts extends Model
{
    protected $table ='qt_higcharts';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * 格式化时间,只显示月份与日份 function
     *
     * @param [type] $value
     * @return void
     */
    public function getDateAttribute($value)
    {
        return substr($value,5,5);
    }


    public function getBindAttribute($value)
    {
        return  is_null($value) ? 0 : $value;
    }


}
