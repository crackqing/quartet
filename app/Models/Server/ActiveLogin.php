<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

class ActiveLogin extends Model
{
    protected $primaryKey = 'id';

    protected $table ='qt_active_login';
    // protected $fillable $guarded
    protected $guarded = [];
}
