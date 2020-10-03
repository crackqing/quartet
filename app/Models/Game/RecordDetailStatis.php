<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
class RecordDetailStatis extends BaseModel
{
    //protected $connection ='test'

    protected $table ='sf_game_record_detail_statis';

    // protected $fillable $guarded
    protected $guarded = [];



    public function getYazhuTotalAttribute($value)
    {
        return $value / 10000;
    }

    public function getDefenTotalAttribute($value)
    {
        return $value / 10000;
    }

    public function getChoushuiTotalAttribute($value)
    {
        return $value / 10000;
    }

    public function getYingliTotalAttribute($value)
    {
        return $value / 10000;
    }


}
