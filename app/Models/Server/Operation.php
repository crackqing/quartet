<?php

namespace App\Models\Server;

use Illuminate\Database\Eloquent\Model;
/**
 * 访问记录 class
 */
class Operation extends Model
{
    protected $primaryKey = 'id';

    protected $table ='qt_active_record';
    // protected $fillable $guarded
    protected $guarded = [];



    public function user()
    {
        return $this->belongsTo('App\User','manager_id','id')->withDefault();
    }

}
