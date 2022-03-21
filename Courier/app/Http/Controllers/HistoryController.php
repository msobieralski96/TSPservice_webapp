<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Parcel;
use App\User;
use App\Role;
use App\ParcelHistory;
use Input;
use Redirect;
use View;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function show(Request $request, Parcel $parcel)
    {
        if(Auth::check()){
            $histories = ParcelHistory::where('parcel_id', $parcel->id)->get();
            return View::make('history.show')
                ->with('parcel', $parcel)
                ->with('histories', $histories);
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby wyświetlać przesyłki");
        }
    }

    public function store(Request $request)
    {
        if(!Auth::check()){
            $courier_phone = $request->input('courier_phone');
            $courier_email = $request->input('courier_email');
            $parcelId = $request->input('parcel');
            $parcel = Parcel::where('id', $parcelId)->first();
            if($parcel !== null) {
                $histories = ParcelHistory::where('parcel_id', $parcelId)->get();
                return View::make('history.show')
                    ->with('parcelCode', $parcel->SSCC_number)
                    ->with('courier_phone', $courier_phone)
                    ->with('courier_email', $courier_email)
                    ->with('histories', $histories);
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


