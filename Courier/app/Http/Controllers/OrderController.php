<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use View;
use Redirect;
//use Input;
use App\Parcel;
use App\Role;
use App\User;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function show(Request $request)
    {
        if(Auth::check()) {
            $dt = Carbon::today();
            $month = $dt->month;
            if (strlen($month) == 1){
                $month = "0".$month;
            }
            $day = $dt->day;
            if (strlen($day) == 1){
                $day = "0".$day;
            }
            $year = $dt->year;
            $date = $month."/".$day."/".$year;
            return View::make('order.prepare')
                ->with('date', $date);
        } else {
            return Redirect::to('parcels')
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }
    }

    public function prepare(Request $request)
    {
        if(Auth::check()) {
            $validatedData = $request->validate([
                'courier' => 'required',
                'date' => 'required|date'
            ]);

            $courier = $request->input('courier');
            $date = $request->input('date');
            $date = $this->getDate($date);
            return Redirect::action('OrderController@show2',
                ['courier' => $courier,
                'date' => $date]);
        } else {
            return Redirect::to('parcels')
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }
    }

    public function getDate($date){
        if(preg_match("/^\d{1}\/\d{1}\/\d{4}/", $date)){
            $month = "0".substr($date, 0, 1);
            $day = "0".substr($date, 2, 1);
            $year = substr($date, 4, 4);
        } elseif(preg_match("/^\d{1}\/\d{2}\/\d{4}/", $date)){
            $month =  "0".substr($date, 0, 1);
            $day = substr($date, 2, 2);
            $year = substr($date, 5, 4);
        } elseif(preg_match("/^\d{2}\/\d{1}\/\d{4}/", $date)){
            $month = substr($date, 0, 2);
            $day = "0".substr($date, 3, 1);
            $year = substr($date, 5, 4);
        } elseif(preg_match("/^\d{2}\/\d{2}\/\d{4}/", $date)){
            $month = substr($date, 0, 2);
            $day = substr($date, 3, 2);
            $year = substr($date, 6, 4);
        } elseif(preg_match("/^\d{1}\/\d{1}\/\d{2}/", $date)){
            $month = "0".substr($date, 0, 1);
            $day = "0".substr($date, 2, 1);
            $year = substr($date, 4, 2);
        } elseif(preg_match("/^\d{1}\/\d{2}\/\d{2}/", $date)){
            $month = "0".substr($date, 0, 1);
            $day = substr($date, 2, 2);
            $year = substr($date, 5, 2);
        } elseif(preg_match("/^\d{2}\/\d{1}\/\d{2}/", $date)){
            $month = substr($date, 0, 2);
            $day = "0".substr($date, 3, 1);
            $year = substr($date, 5, 2);
        } elseif(preg_match("/^\d{2}\/\d{2}\/\d{2}/", $date)){
            $month = substr($date, 0, 2);
            $day = substr($date, 3, 2);
            $year = substr($date, 6, 2);
        } else {
            $month = substr($date, 0, 2);
            $day = substr($date, 3, 2);
            $year = substr($date, 6, 4);
        }
        return $year."-".$month."-".$day;
    }

    public function show2(Request $request, $courier, $date){
        if(Auth::check()) {
            if(Auth::user()->isAdmin() || Auth::user()->id == $courier){
                //$data["courier"] = $courier;
                //$data["date"] = $date;
                return View::make('order.prepare2'/*, $data*/)
                    ->with('courier', $courier)
                    ->with('date', $date);
            } else {
                return Redirect::back()
                    ->with('error', "Nie masz odpowiednich uprawnień, aby wyznaczać przesyłki innym kurierom");
            }
        } else {
            return Redirect::to('parcels')
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }
    }

    public function prepare2(Request $request, $courier, $date){
        if(Auth::check()) {
            $validatedData = $request->validate([
                'localization' => 'required'
            ]);

            $localization = $request->input('localization');
            return Redirect::action('OrderController@create',
                ['courier' => $courier,
                'date' => $date,
                'localization' => $localization]);
        } else {
            return Redirect::to('parcels')
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }

    }

    public function create(Request $request, $courier, $date, $localization)
    {
        if(Auth::check()) {
            $parcels = collect();
            if(Auth::user()->isAdmin() || Auth::user()->id == $courier){
                $parcels = Parcel::where(function ($query) use ($courier, $date, $localization) {
                               $query->where('courier_id', $courier)
                                   ->whereRaw('date_of_delivery::text like ?', $date." 00:00:00")
                                   ->where('current_address', $localization);
                           })->orWhere(function ($query) use ($courier, $date, $localization) {
                               $query->where('courier_id', $courier)
                                   ->whereRaw('date_of_get_delivery::text like ?', $date." 00:00:00")
                                   ->where('current_address', $localization);
                           })->get();
            } else {
                return Redirect::back()
                    ->with('error', "Nie masz odpowiednich uprawnień, aby wyznaczać przesyłki innym kurierom");
            }
            $courier = User::where('id', $courier)->first();
            return View::make('order.index')
                ->with('parcels', $parcels)
                ->with('courier', $courier)
                ->with('localization', $localization)
                ->with('date', $date);
        } else {
            return Redirect::to('parcels')
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }
    }

    public function store(Request $request, $courier, $date, $localization)
    {
        if(Auth::check()) {
            $parcelInput = $request->input('parcel');
            $address_typeInput = $request->input('address_type');
            //$parcelInput = json_decode(json_encode($parcelInput), true);
            foreach($parcelInput as $key => $iParcel){
                $iParcel = json_decode($iParcel, true);
                $parcel = Parcel::where('id', $iParcel["id"])->first();
                if($address_typeInput[$key] == "Adres docelowy"){
                    $parcel->deliver_order = $key+1;
                    $parcel->save();
                } elseif($address_typeInput[$key] == "Adres nadawcy"){
                    $parcel->get_order = $key+1;
                    $parcel->save();
                }
            }
            $couriername = User::where('id', $courier)->first()->name;

            return Redirect::action('OrderController@create',
                ['courier' => $courier,
                'date' => $date,
                'localization' => $localization])
                //->with('message', get_class(json_decode(json_encode($parcelInput[0], true))));
                //->with('message', get_class(json_decode($parcelInput[0], true)));
                //->with('message', json_decode($parcelInput[0], true)["id"]);
                ->with('message', "Zmieniono kolejność przesyłek dla kuriera "
                    .$couriername." w dniu ".$date.", dla lokalizacji: ".$localization.".");
        } else {
            return Redirect::to('parcels')
                ->with('error', "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji");
        }

    }
}

View::composer('order.prepare', function($view) {
    $roles = Role::where('role', 2)->orWhere('role', 4)->get();
    $allUsers = User::all();
    $couriers = collect();
    $noUser = new User();
    $noUser->id = null;
    $noUser->name = '';
    $couriers->push($noUser);
    foreach ($roles as $role){
        foreach ($allUsers as $user){
            if ($role->user_id == $user->id){
                $couriers->push($user);
            }
        }
    }
    if(count($couriers) > 0){
        $courier_options = $couriers->pluck('name', 'id');
    } else {
        $courier_options = array(null, 'nieokreślony');
    }
    $view->with('courier_options', $courier_options);
        //->with('localization_options', $localization_options);
});

View::composer('order.prepare2', function($view) {

    //$data contains category_id
    $courier = $view["courier"];
    $date = $view["date"];

    $parcels = Parcel::where(function ($query) use ($courier, $date) {
                   $query->where('courier_id', $courier)
                       ->whereRaw('date_of_delivery::text like ?', $date." 00:00:00");
                       //->where('CAST(date_of_delivery.value AS TEXT)', 'LIKE', $date);
               })->orWhere(function ($query) use ($courier, $date) {
                   $query->where('courier_id', $courier)
                       ->whereRaw('date_of_get_delivery::text like ?', $date." 00:00:00");
                       //->where('CAST(date_of_get_delivery.value AS TEXT)', 'LIKE', $date);
               })->get();

    $addresses = collect();
    foreach ($parcels as $parcel){
        if (count($addresses) > 0){
            $addFlag = true;
            foreach ($addresses as $address){
                if ($address == $parcel->address){
                    $addFlag = false;
                }
            }
            if($addFlag){
                $addresses->push($parcel->current_address);
            }
        } else {
            $addresses->push($parcel->current_address);
        }
    }
    if(count($addresses) > 0){
        $localization_options = array();
        foreach($addresses as $address){
            $localization_options[$address] = $address;
            //$localization_options = array_push($localization_options, $address);
        }
    } else {
        $localization_options = array("brak" => "brak");
    }

    $view->with('localization_options', $localization_options);
});
