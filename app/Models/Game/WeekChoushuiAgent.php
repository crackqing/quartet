<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use App\Models\Game\DailyChoushuiAgent as dailyAgent;

class WeekChoushuiAgent extends Model
{
    protected $table ='sf_week_choushui_agent';

    public $timestamps = false;

    // protected $fillable $guarded
    protected $guarded = [];


    protected $appends = ['selfArry'];

    public function getSelfArryAttribute()
    {
            $daily = dailyAgent::where('time_rand',$this->time_rand)
                        ->where('bind_id',$this->bind_id)
                        ->first();
            
            $choushui = $daily->total_choushui ?? 0;
            $level = agent_daily($choushui);
            $data = [
                'self_performance'  => $choushui,
                'self_commission'   => floor($choushui/ 10000 ) * $level[1],
            ];

            return $data;
    }

}
