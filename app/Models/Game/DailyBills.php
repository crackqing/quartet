<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
class DailyBills extends BaseModel
{
    protected $table ='qt_daily_bills';

    protected $guarded = [];
    
    protected $hidden = [
        'updated_at'
    ];


    public function getAccountAttribute($value)
    {
        return $value / 10000;
    }

    public function getBankAttribute($value)
    {
        return $value / 10000;
    }


    public function getCoinsAttribute($value)
    {
        return $value / 10000;
    }
    


    public function getTimeAttribute($value)
    {
        return substr($value,0,10);
    }

}
