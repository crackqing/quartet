<?php

namespace App\Observers\Game;

use App\Models\Game\RecordDetail;

class RecordDetailObserver
{
    /**
     * 监听用户创建事件 function
     *
     * @param RecordDetail $recordDetail
     * @return void
     */
    public function created(RecordDetail $recordDetail)
    {
        
    }

    /**
     * 监听用户删除事件 function
     *
     * @param RecordDetail $recordDetail
     * @return void
     */
    public function deleting(RecordDetail $recordDetail)
    {

    }
}
