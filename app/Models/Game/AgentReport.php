<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class AgentReport extends Model
{
    protected $table ='qt_agent_report';

    protected $guarded = [];

    protected $hidden = [
        'updated_at'
    ];


    public function getCreatedAtAttribute($value)
    {
        return substr($value,0,10);
    }
    public function manager()
    {
        return $this->belongsTo('App\User','manager_id','id')->withDefault();
    }

}
