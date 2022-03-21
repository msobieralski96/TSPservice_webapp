<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use View;
use Redirect;
use App\Role;

class RegistrationController extends Controller
{

    public function create(Request $request)
    {
        if(!Auth::user()){
            $user = new User();
            return view('registration.create')
                ->with('user', $user)
                ->with('method', 'post');
        } else {
            return Redirect::back()
                ->with('error', "Wyloguj się, aby móc utworzyć użytkownika.");
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'phone_number' => 'numeric|nullable|unique:users',
            'password' => 'required|string|min:5|confirmed',
            //'password_confirmation' => 'required|min:5'
        ]);

        //$user = User::create(request(['name', 'email', 'password']));

        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone_number = $request->get('phone_number');
        $user->password = bcrypt($request->get('password'));

        if($user->save()){
            auth()->login($user);
            $role = new Role;
            $role->user_id = $user->id;
            $role->role = 3;
            $role->save();
            return Redirect::to('logout/accountCreated');
            //return Redirect::to('users/' . $user->id)
            //    ->with('message', 'Utworzono nowe konto użytkownika.');
        } else {
            return Redirect::back()
                ->with('error', 'Nie udało się utworzyć konta użytkownika.');
        }
    }

}

