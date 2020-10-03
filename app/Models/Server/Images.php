<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $primaryKey = 'id';

    protected $table ='images';
    // protected $fillable $guarded
    protected $guarded = [];
}
