<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use View;
use Redirect;
use App\Parcel;
use App\Role;
use App\User;
use Carbon\Carbon;

class ApiOrderController extends Controller
{
    public function prepare(Request $request)
    {
        $user = $request->user();
        if($user !== null && !($user->isNotConfirmed())){
            $validatedData = $request->validate([
                'date' => 'required|date'
            ]);

            $date = $request->input('date');
            //$date = $this->getDate($date);

            if($user->isAdmin()){
                $courier = 0;//"admin"
                $parcels = Parcel::where(function ($query) use ($date) {
                               $query->whereRaw('date_of_delivery::text like ?', $date." 00:00:00");
                           })->orWhere(function ($query) use ($date) {
                               $query->whereRaw('date_of_get_delivery::text like ?', $date." 00:00:00");
                           })->get();
            } else {
                $courier = $request->user()->id;
                $parcels = Parcel::where(function ($query) use ($courier, $date) {
                               $query->where('courier_id', $courier)
                                   ->whereRaw('date_of_delivery::text like ?', $date." 00:00:00");
                           })->orWhere(function ($query) use ($courier, $date) {
                               $query->where('courier_id', $courier)
                                   ->whereRaw('date_of_get_delivery::text like ?', $date." 00:00:00");
                           })->get();
            }

            return response()->json([
                'parcels' => $parcels,
                'courier' => $courier,
                'date' => $date,
                'localization_list' => $this->getLocalizationList($parcels),
                'result' => 'true'
            ], 201);

        } else {
            return response()->json([
                'message' => "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji",
                'result' => 'false'
            ], 401);
        }
    }

    public function getLocalizationList($parcels) {
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
            }
        } else {
            $localization_options = array("brak" => "brak");
        }

        return $localization_options;
    }

    public function prepare2(Request $request){

        $user = $request->user();
        if($user !== null && !($user->isNotConfirmed())){

            $validatedData = $request->validate([
                'date' => 'required|date',
                'localization' => 'required'
            ]);

            $date = $request->input('date');
            $localization = $request->input('localization');

            if($user->isAdmin()){
                $courier = 0;//"admin"
                $parcels = Parcel::where(function ($query) use ($date, $localization) {
                               $query->whereRaw('date_of_delivery::text like ?', $date." 00:00:00")
                                   ->where('current_address', $localization);
                           })->orWhere(function ($query) use ($date, $localization) {
                               $query->whereRaw('date_of_get_delivery::text like ?', $date." 00:00:00")
                                   ->where('current_address', $localization);
                           })->get();
            } else {
                $courier = $request->user()->id;
                $parcels = Parcel::where(function ($query) use ($courier, $date, $localization) {
                               $query->where('courier_id', $courier)
                                   ->whereRaw('date_of_delivery::text like ?', $date." 00:00:00")
                                   ->where('current_address', $localization);
                           })->orWhere(function ($query) use ($courier, $date, $localization) {
                               $query->where('courier_id', $courier)
                                   ->whereRaw('date_of_get_delivery::text like ?', $date." 00:00:00")
                                   ->where('current_address', $localization);
                           })->get();
            }

            return response()->json([
                'parcels' => $parcels,
                'courier' => $courier,
                'date' => $date,
                'localization' => $localization,
                'result' => 'true'
            ], 201);

        } else {
            return response()->json([
                'message' => "Nie masz odpowiednich uprawnień, aby uzyskać dostęp do tej funkcji",
                'result' => 'false'
            ], 401);
        }
    }

}
