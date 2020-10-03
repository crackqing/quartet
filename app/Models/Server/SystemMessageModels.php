<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

class SystemMessageModels extends Model
{
    protected $primaryKey = 'id';

    protected $table ='qt_system_messages';

    protected $guarded = [];
}
