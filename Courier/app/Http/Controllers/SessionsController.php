<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use View;
use Redirect;
use Input;

class SessionsController extends Controller
{

    public function create(Request $request)
    {
        if(!Auth::user()){
            return View::make('login');
        } else {
            return Redirect::back()
                ->with('error', "Już jesteś zalogowany!");
        }
    }

    public function store(Request $request)
    {
        if(Auth::attempt(Input::only('name', 'password'))) {
            if(Auth::user()->isNotConfirmed()){
                return Redirect::to('logout/accountNotConfirmed');
            }
            return Redirect::intended('/');
        } else {
            return Redirect::back()
                ->withInput()
                ->with('error', "Podano złą nazwę użytkownika lub hasło!");
        }
    }

    public function destroy(Request $request)
    {
        if(Auth::user()){
            Auth::logout();
            return Redirect::to('home')
                ->with('message', 'Pomyślnie wylogowano.');
        } else {
            return Redirect::to('home')
                ->with('error', 'Nie jesteś zalogowany!');
        }
    }

    public function destroyWithMessage(Request $request, String $message)
    {
        if(Auth::user()){
            Auth::logout();
            if($message == 'accountCreated'){
                return Redirect::to('home')
                    ->with('message', 'Utworzono nowe konto użytkownika. Wymagana aktywacja konta przez administratora, żeby móc korzystać z konta.');
            } elseif($message == 'accountNotConfirmed'){
                return Redirect::to('home')
                    ->with('error', 'Wymagana aktywacja konta przez administratora, żeby móc korzystać z konta.');
            }
            return Redirect::to('home')
                ->with('message', $message);
        } else {
            return Redirect::to('home')
                ->with('error', 'Nie jesteś zalogowany!');
        }

    }

}

