<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;



class RecordDetailEvery extends Model
{
    //protected $connection ='test'

    protected $table ='sf_game_record_detail_every';

    // protected $fillable $guarded
    protected $guarded = ['yingli'];

    protected $hidden = ['updated_at'];

    public function getYazhuAttribute($value)
    {
        return $value / 10000;
    }

    public function getDefenAttribute($value)
    {
        return $value / 10000;
    }

    public function getChoushuiAttribute($value)
    {
        return $value / 10000;
    }


}
