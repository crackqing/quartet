<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class DailyChoushuiAgent extends Model
{
    protected $table ='sf_daily_choushui_agent';

    public $timestamps = false;

    // protected $fillable $guarded
    protected $guarded = [];

    /**
     * 关联对应的用户ID，显示上级ID是谁 function
     *
     * @return void
     */
    public function user()
    {
        
    }
}
