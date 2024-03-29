<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = array('name', 'address');

    public $timestamps = false;
}
