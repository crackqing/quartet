<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table ='qt_settings';

    public $timestamps = false;

    // protected $fillable $guarded
    protected $guarded = [];



}
