<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use View;
use Redirect;
use App\Role;
use App\Parcel;
use Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::check()){
            $parcels = collect();
            if(Auth::user()->isAdmin()){
                $users = User::all();
                return View::make('user.index')
                    ->with('users', $users);
            } elseif(Auth::user()->isCourier()){
                $info = User::where('id', Auth::user()->id)->first();
                return View::make('user.detail')
                    ->with('info', $info);
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz odpowiednich uprawnień, aby wyświetlać użytkowników");
        }
    }

    public function show(Request $request, User $user)
    {
        if(Auth::check()){
            //$info = User::where('id', Auth::user()->id)->first();
            if(Auth::user()->isAdmin() || Auth::user()->id == $user->id){
                $info = User::where('id', $user->id)->first();
                    return View::make('user.detail')
                        ->with('info', $info);
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz uprawnień, żeby przeglądać dane innych użytkowników");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie jesteś zalogowany.");
        }
    }

    public function editView(Request $request, User $user)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin() || Auth::user()->id == $user->id){
                return View::make('user.edit')
                    ->with('user', $user)
                    ->with('method', 'put');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz uprawnień, żeby edytować dane innych użytkowników");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz uprawnień, żeby edytować dane innych użytkowników");
        }
    }

    public function editPasswordView(Request $request, User $user)
    {
        if(Auth::check()){
            if(Auth::user()->id == $user->id){
                return View::make('user.editpassword')
                    ->with('user', $user)
                    ->with('method', 'put');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz uprawnień, żeby zmienić hasło innego użytkownika");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz uprawnień, żeby zmienić hasło innego użytkownika");
        }
    }

    public function destroyView(Request $request, User $user)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()) {
                return View::make('user.edit')
                    ->with('user', $user)
                    ->with('method', 'delete');
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz uprawnień, żeby usunąć użytkownika z systemu");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz uprawnień, żeby usunąć użytkownika z systemu");
        }
    }

    public function edit(Request $request, User $user)
    {
        if(Auth::check()) {
            if(Auth::user()->isAdmin() || Auth::user()->id == $user->id){
                if(Auth::user()->id == $user->id){
                    $validatedData = $request->validate([
                        'email' => 'required|string|email|max:100|unique:users,email,'.$user->id,
                        'phone_number' => 'numeric|nullable|unique:users,phone_number,'.$user->id
                    ]);
                    $user->email = $request->input('email');
                    $user->phone_number = $request->input('phone_number');
                    $user->save();
                }
                if(Auth::user()->isAdmin()){
                    $validatedData = $request->validate([
                        'role' => 'required'
                    ]);
                    $role = Role::where('user_id', $user->id)->first();
                    $role->role = $request->input('role');
                    $role->save();
                }
                return Redirect::to('users/' . $user->id)
                    ->with('message', "Edytowano dane użytkownika");
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz uprawnień, żeby edytować dane innych użytkowników");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz uprawnień, żeby edytować dane innych użytkowników");
        }
    }

    public function editPassword(Request $request, User $user)
    {
        if(Auth::check()) {
            if(Auth::user()->id == $user->id){
                if(!(Hash::check($request->get('currentpassword'), Auth::user()->password))) {
                    return Redirect::back()
                        ->with("error", "Podane hasło nie zgadza się z twoim aktualnym hasłem");
                }

                if(strcmp($request->get('currentpassword'), $request->get('newpassword')) == 0){
                    return Redirect::back()
                        ->with("error", "Nowe hasło nie może być takie same, jak obecne hasło");
                }

                $validatedData = $request->validate([
                    'currentpassword' => 'required|string|min:5',
                    'newpassword' => 'required|string|min:5|confirmed'
                ]);

                //$user->password = $request->get('newpassword');
                $user->password = bcrypt($request->get('newpassword'));
                $user->save();
                return Redirect::to('users/' . $user->id)
                    ->with('message', "Zmieniono hasło użytkownika");
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz uprawnień, żeby zmienić hasło innego użytkownika");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz uprawnień, żeby zmienić hasło innego użytkownika");
        }
    }

    public function destroy(Request $request, User $user)
    {
        if(Auth::check()){
            if(Auth::user()->isAdmin()) {
                $parcels = Parcel::where('courier_id', $user->id)->get();
                foreach($parcels as $parcel){
                    $parcel->courier_id = null;
                    $parcel->save();
                }
                $role = Role::where('user_id', $user->id)->first();
                $role->delete();
                $user->delete();
                return Redirect::to('users')
                    ->with('message', "Usunięto użytkownika!");
            } else {
                return Redirect::to('parcels/')
                    ->with('error', "Nie masz uprawnień, żeby usunąć użytkownika z systemu");
            }
        } else {
            return Redirect::to('home/')
                ->with('error', "Nie masz uprawnień, żeby usunąć użytkownika z systemu");
        }
    }
}
