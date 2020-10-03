<?php
namespace App\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\ActiveContracts;

use App\Models\Server\ActiveLogin;
class ActiveEloquent extends BaseRepository implements ActiveContracts
{

    public function model()
    {
        return ActiveLogin::class;
    }

    public function activeLog($request,$id,$status)
    {
        $data = [
            'email' => $request->email,
            'password'  => $status == 0 ? '********' : $request->password,
            'captcha'   => 'undefined', //只有登录错误大于4次的情况下开启,10次限制五分
            'type'  => 1,
            'ip'    => $request->ip(),
            'ua'    => $request->userAgent(),
            'status'    => $status,
            'user_id'   => $id
        ];
        $active = $this->model;
        $active->create($data);
    }
}