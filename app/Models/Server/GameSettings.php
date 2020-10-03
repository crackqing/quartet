<?php

namespace App\Models\Server;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
/**
 * 游戏设置模型,创建,更新,删除,编辑 都需要通知游戏方面接口 class
 */
class GameSettings extends Model
{    
    protected $primaryKey = 'id';

    protected $table ='qt_game_settings';
    protected $guarded = [];

    protected $appends = ['kind_name'];

    public  $timestamps = false;
    //$touches 属性是个数组，包含了在评论的创建、保存和删除时会引起“触发”的关联信息。

    /**
     * trait 共用，或者创建 interface  function
     *
     * @return void
     */
    // public function cacheKey()
    // {
    //     return sprintf(
    //         "%s/%s-%s",
    //         $this->getTable(),
    //         $this->getKey(),
    //         $this->updated_at->timestamp
    //     );
    // }

    // public function getCachedCountAttribute()
    // {
    //     return Cache::remember($this->cacheKey() . ':kindid', 15, function () {
    //         // return $this->comments->count();
    //     });
    // }



    
    /**
     * 配置kindid 中文显示 function
     *
     * @return void
     */
    public function getKindidAttribute($value)
    {
        return $value;
    }

    public function getKindNameAttribute()
    {
        $data = [
            '1000'  => '百人牛牛',
            '1001'  => '奔驰宝马',
            '1002'  => '飞禽走兽',
            // '1003'  => '比大小',
            '1004'  => '百家乐',
            '1005'  => '水浒传',
            '1015'  => '红黑大战',
            '1100'  => '摇钱树',
            '10000' => '所有'
        ];
        return $data[$this->kindid] ?? '';
    }

    public function getTaxAttribute($value)
    {
        if ($this->kindid == 1005) {
            return $value;
        }
        return $this->kindid != 1100 ? (int) $value : null;
    }


    /**
     * 属性只能_不能大写设置 这里是用于发给游戏 function
     *
     * @param [type] $value
     * @return void
     */
    // public function getenterLimitAttribute($value)
    // {
    //     return $value / 10000;
    // }



    // public function getMinCannonAttribute($value)
    // {
    //     return $value / 10000;
    // }


    // public function getMaxCannonAttribute($value)
    // {
    //     return $value / 10000;
    // }



}
