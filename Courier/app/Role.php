<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = array('role', 'user_id');

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

