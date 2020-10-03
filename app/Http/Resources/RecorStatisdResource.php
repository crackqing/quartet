<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


//资源类用于将单个模型转化为数组，而资源集合类用于将模型集合转化为数组.并不是每种类型的模型都需要定义一个资源集合类，因为所有资源类都提供了一个 collection 方法立马生成一个特定的资源集合： 不过，如果你需要自定义与集合一起返回的元数据，就需要定义一个资源集合类了：
class RecorStatisdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //model::collectiono 关联响应
        return [
            'id'    => $this->id,
            'yazhuTotal'  => $this->yazhuTotal,
            'defenTotal'    => $this->defenTotal,
            'yingliTotal'   => $this->yingliTotal,
            'choushuiTotal' => $this->choushuiTotal,
            'coinsTotal'    => $this->coinsTotal,
            'bankTotal' => $this->bankTotal,
            'time'  => $this->time,
            'kindid'    => $this->kindid,
            'tid'   => $this->tid,
            'uid'   => $this->uid
        ];
    }
}
