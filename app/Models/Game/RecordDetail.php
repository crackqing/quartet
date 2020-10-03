<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class RecordDetail extends Model
{
    //protected $connection ='test'

    protected $table ='sf_game_record_detail';

    public $timestamps = false;

    // protected $fillable $guarded
    protected $guarded = [];

    public function getYazhuAttribute($value)
    {
        return $value / 10000;
    }
    public function getDefenAttribute($value)
    {
        return $value / 10000;
    }
    public function getYingliAttribute($value)
    {
        return $value / 10000;
    }
    public function getChoushuiAttribute($value)
    {
        return $value / 10000; 
    }
    public function getCoinsAttribute($value)
    {
        return $value / 10000;
    }
    public function getBankAttribute($value)
    {
        return $value / 10000;
    }

    /**
     * 充值关联 function
     *
     * @return void
     */
    public function users()
    {
        return $this->hasMany('App\User', 'email', 'uid');
    }    

}
