<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App\Role;

class ApiUserController extends Controller
{
    /**
     * Create user (API)
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] phone_number
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'phone_number' => 'numeric|nullable|unique:users',
            'password' => 'required|string|min:5|confirmed',
            //'password_confirmation' => 'required|same:password',
        ]);
        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone_number = $request->get('phone_number');
        $user->password = bcrypt($request->get('password'));

        $user->save();
        $role = new Role;
        $role->user_id = $user->id;
        $role->role = 3;
        $role->save();

        return response()->json([
            'message' => 'Utworzono nowe konto użytkownika. Żeby móc się zalogować, konto musi być zweryfikowane przez administratora.',
            'result' => 'true'
        ], 201);
    }

    /**
     * Login user and create token (API)
     *
     * @param  [string] name
     * @param  [string] password
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     * @return [string] result
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        $request->validate([
            'name' => 'required|string|min:3|max:50',
            'password' => 'required|string|min:5',
        ]);

        $credentials = request(['name', 'password']);

        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Nieautoryzowany',
                'result' => 'false'
            ], 401);
        }

        $user = $request->user();

        if($user->isNotConfirmed()){
            //$request->user()->token()->revoke();
            return response()->json([
                'message' => 'Konto wymaga aktywacji przez administratora!',
                'result' => 'false'
            ], 401);
        }
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'username' => $user->name,
            'result' => 'true'
        ]);
    }

    /**
     * Logout user (Revoke the token) (API)
     *
     * @return [string] message
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Wylogowano pomyślnie',
            'result' => 'true'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * Get the authenticated User
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }
}
