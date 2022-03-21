<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParcelHistory extends Model
{
    //
    protected $fillable = array('parcel_id', 'date_of_action', 'state_of_delivery', 'localisation');

    //protected $dates = ['date_of_delivery'];

    public $timestamps = false;

    public function parcel()
    {
        return $this->belongsTo('App\Parcel', 'parcel_id');
    }
}
