<?php

namespace App\Models\Ssc;

use Illuminate\Database\Eloquent\Model;

class Touzhu extends Model
{
    protected $connection = 'ssc_db';

    protected $primaryKey = 'id';

    protected $table ='touzhu';

    public $timestamps = false;


    protected $guarded = [];
}
