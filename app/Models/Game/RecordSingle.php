<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class RecordSingle extends Model
{
    //protected $connection ='test'

    protected $table ='sf_game_record_single';

    // protected $fillable $guarded
    protected $guarded = [];

    protected $hidden = [
        'coinsTotal','bankTotal','updated_at'
    ];

    public function User()
    {
        return $this->belongsTo('App\User','email')->withDefault();
    }


    public function getYzhutotalAttribute($value)
    {
        return $value / 10000;
    }
    public function getDefentotalAttribute($value)
    {
        return $value / 10000;
    }
    public function getYinglitotalAttribute($value)
    {
        return $value / 10000;
    }
    public function getGameBalanceAttribute($value)
    {
        return $value * 10000;
    }


}
