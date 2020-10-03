<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
/**
 * 每日抽水的统计,所有玩家的处理 class
 */
class DailyChoushui extends Model
{
    protected $table ='sf_daily_choushui_uid';

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
