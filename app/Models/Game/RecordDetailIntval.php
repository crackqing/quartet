<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use App\User;
class RecordDetailIntval extends Model
{
    protected $table ='sf_game_record_detail';

    public $timestamps = false;

    protected $guarded = [];

    protected $appends = ['bindId'];
    /**
     * YL的转整为绝对值 function
     *
     * @param [type] $value
     * @return void
     */
    public function getYingliAttribute($value)
    {
        if ($this->kindid == 1100) {
            return  abs( $value / 100000 );
        }
        return  abs( $value / 10000 );
    }
    /**
     * 获取对应的绑定ID处理 function
     *
     * @param [type] $value
     * @return void
     */
    public function getBindIdAttribute($value)
    {
        $bind = User::where('agent_id',$this->uid)->value('bind_id');

        return $bind ?? 0;
    }
}
