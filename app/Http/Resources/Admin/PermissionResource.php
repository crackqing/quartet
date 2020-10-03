<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'name'  => $this->name,
            'display_name'    => $this->display_name,
            'description'   => $this->description,
        ];
    }
}
