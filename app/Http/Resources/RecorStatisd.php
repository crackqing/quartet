<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecorStatisd extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /**
         * Laravel文档说明 when 方法就可以用于在满足某种条件的前提下添加属性到资源响应 ($this->when)
         * 
         * Models::collection($this->whenLoaded('models')) |whenPivotLoaded
         */
        return ['data'  => $this->collection];
    }
}
