<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class UserArray extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email'  => $this->email,
            'bind_id'  => $this->bind_id,
            'mobile'  => $this->mobile,
            'agent_id'  => $this->agent_id,
            'agent_nickname'  => $this->agent_nickname,
            'status'  => $this->status,
            'created_at'  => $this->created_at,
            'roles'  => $this->whenPivoLoaded('role_users',function(){
                return $this->pivot->expires_at;
            }),
        ];
    }
}
