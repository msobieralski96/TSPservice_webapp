<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Place;
use App\User;
use Input;
use Redirect;
use View;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()){
                $addresses = Place::all();
                return View::make('addresses.index')
                    ->with('addresses', $addresses);
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
        }
    }

    public function create(Request $request)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()) {
                $address = new Place;
                return View::make('addresses.edit')
                    ->with('address', $address)
                    ->with('method', 'post');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
        }
    }

    public function store(Request $request)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()){
                $validatedData = $request->validate([
                    'name' => 'required|min:3',
                    'address' => 'required|min:3|unique:places'
                ]);

                $address = Place::create(Input::all());

                if($address->save()){
                    return Redirect::to('addresses/')
                        ->with('message', 'Dodano nowe miejsce predefiniowane');
                } else {
                    return Redirect::back()
                        ->with('error', 'Nie udało się dodać miejsca predefiniowanego!');
                }
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
        }
    }

    public function editView(Request $request, Place $address)
    {
        if(Auth::check()) {
            if(Auth::user()->isAdmin()){
                return View::make('addresses.edit')
                    ->with('address', $address)
                    ->with('method', 'put');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
        }
    }

    public function destroyView(Request $request, Place $address)
    {
        if(Auth::check()) {
            if(Auth::user()->isAdmin()){
                return View::make('addresses.edit')
                    ->with('address', $address)
                    ->with('method', 'delete');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
        }
    }

    public function edit(Request $request, Place $address)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()) {
                $validatedData = $request->validate([
                    'name' => 'required|min:3',
                    'address' => 'required|min:3|unique:places,address,'.$address->id
                ]);

                $address -> update(Input::all());
                return Redirect::to('addresses/')
                    ->with('message', 'Edytowano właściwości miejsca predefiniowanego!');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
        }
    }

    public function destroy(Request $request, Place $address)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()) {
                $address -> delete();
                return Redirect::to('addresses/')
                    ->with('message', 'Usunięto miejsce predefiniowane!');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby korzystać z tej funkcji");
        }
    }
}

