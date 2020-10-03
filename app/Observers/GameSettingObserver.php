<?php

namespace App\Observers;
use App\Models\Server\GameSettings;

class GameSettingObserver
{
    /**
     * 监听游戏配置创建事件. 更新指定的对应kindid配置文件
     *
     * @param GameSettings $GameSettings
     * @return void
     */
    public function created(GameSettings $GameSettings)
    {
        \Log::info('gameSettings====>created',['gameSettings'=>$GameSettings]);
    
    }
    /**
     * 监听游戏配置删除事件.
     *
     * @param GameSettings $GameSettings
     * @return void
     */
    public function deleted(GameSettings $GameSettings)
    {
        \Log::info('gameSettings====>deleted',['gameSettings'=>$GameSettings]);
    
    }

    /**
     * 监听游戏配置保存事件.
     *
     * @param GameSettings $GameSettings
     * @return void
     */
    public function saved(GameSettings $GameSettings)
    {
        \Log::info('gameSettings====>saved',['gameSettings'=>$GameSettings]);
    
    }

    /**
     * 监听游戏配置更新事件.
     *
     * @param GameSettings $GameSettings
     * @return void
     */
    public function updated(GameSettings $GameSettings)
    {
        \Log::info('gameSettings====>updated',['gameSettings'=>$GameSettings]);
    }


}
