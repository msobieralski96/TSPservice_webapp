<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use View;
use Redirect;
use Input;
use App\Parcel;
use App\User;
use Crypt;

class ClientController extends Controller
{

    public function create(Request $request)
    {
        if(!Auth::user()){
            return View::make('index');
        } else {
            return Redirect::to('parcels');
        }

    }

    public function store(Request $request)
    {
        if(!Auth::user()){
            $parcelCode = $request->input('parcelCode');
            $parcel = Parcel::where('SSCC_number', $parcelCode)->first();
            if($parcel !== null) {
                $courier = User::where('id', $parcel->courier_id)->first();
                if ($courier == null){
                    $courier_phone = null;
                    $courier_email = null;
                } else {
                    $courier_phone = $courier->phone_number;
                    $courier_email = Crypt::encrypt($courier->email);
                }
            return View::make('parcels.detail')
                    ->with('parcel', $parcel)
                    ->with('courier', "unknown")
                    ->with('courier_phone', $courier_phone)
                    ->with('courier_email', $courier_email)
                    ->with('clientRequest', true);
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with('error', "Nie znaleziono przesyłki o danym kodzie w systemie!");
            }
        } else {
            return Redirect::back()
                ->with('error', "Ta funkcja jest dostępna tylko dla niezalogowanych!");
        }
    }
}
