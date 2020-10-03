<?php

namespace App\Models\Ssc;

use Illuminate\Database\Eloquent\Model;

class Kaijiang extends Model
{
    protected $connection = 'ssc_db';

    protected $primaryKey = 'id';

    protected $table ='kaijiang';

    public $timestamps = false;


    protected $guarded = [];
}
