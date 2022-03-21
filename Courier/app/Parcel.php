<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $fillable = array('address', 'state_of_delivery', 'date_of_delivery', 'date_of_get_delivery',
        'courier_id', 'SSCC_number', 'sender_address', 'current_address', 'mass', 'size',
        'client_first_name', 'client_last_name', 'client_phone_number', 'client_email',
        'deliver_order', 'get_order', 'sender_first_name', 'sender_last_name',
        'sender_phone_number', 'sender_email', 'parcel_content');

    //protected $dates = ['date_of_delivery'];

    public function user()
    {
        return $this->belongsTo('App\User', 'courier_id');
    }

    public function parcel_history()
    {
        return $this->hasMany('App\ParcelHistory');
    }

}
