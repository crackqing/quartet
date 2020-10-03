<?php
namespace App\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\UserContracts;

class UserEloquent extends BaseRepository implements UserContracts
{

    public function model()
    {
        return "App\\User";
    }
}