<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Role;
use App\Parcel;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone_number',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //public function setPasswordAttribute($password)
    //{
        //$this->attributes['password'] = bcrypt($password);
    //}

    public function owns(Parcel $parcel){
        return $this->id == $parcel->courier_id;
    }

    public function getRole(){
        $roleObj = Role::where('user_id', $this->id)->first();
        if ($roleObj == null){
            return 3;//isNotConfirmed
        }
        return $roleObj->role;
    }

    public function isAdmin(){
        return ($this->getRole() == 1 || $this->isSuperAdmin());
    }

    public function isCourier(){
        return ($this->getRole() == 2 || $this->isSuperAdmin());
    }

    public function isNotConfirmed(){
        return $this->getRole() == 3;
    }

    public function isSuperAdmin(){
        return $this->getRole() == 4;
    }

    public function canEdit(Parcel $parcel){
        return ($this->isAdmin() || ($this->isCourier() && $this->owns($parcel)));
    }

    public function canAdd(){
        return ($this->isAdmin() || $this->isCourier());
    }

    /*public function getParcelsNumber()
    {
        return $this->hasMany('App\Parcel', 'courier_id');
    }

    public function getOwnedParcels()
    {
        $parcels = Parcel::where('courier_id', $this->id)->get();
        return $parcels;
    }*/

    public function parcel()
    {
        return $this->hasMany('App\Parcel');
    }

    public function role()
    {
        return $this->hasOne('App\Role');
    }
}
