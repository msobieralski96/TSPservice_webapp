<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
|    return view('welcome');
|});
*/

use App\Parcel;
use App\Role;
use App\User;

Route::get('about', function(){
    return View::make('about');
});

Route::get('/', function() {
    if (Auth::check()){
        return Redirect::to('parcels');
    } else {
        return Redirect::to('home');
    }
});

Route::group(array('before'=>'guest|csrf'), function(){

    Route::get('home', 'ClientController@create');

    Route::post('parcel/detail', 'ClientController@store');

    Route::post('parcel/history', 'HistoryController@store');

    Route::get('login', 'SessionsController@create');

    Route::post('login', 'SessionsController@store');

    Route::get('register', 'RegistrationController@create');

    Route::post('register', 'RegistrationController@store');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
});

Route::group(array('before'=>'auth'), function(){

    Route::get('parcels', 'ParcelController@index');

    //Route::get('api/parcels', 'API\ApiParcelController@index');

    Route::get('parcels/create', 'ParcelController@create');

    Route::get('parcels/{parcel}/edit', ['uses' =>'ParcelController@editView']);

    Route::get('parcels/{parcel}/remove', ['uses' =>'ParcelController@destroyView']);

    Route::get('users', ['uses' =>'UserController@index']);

    Route::get('users/{user}/edit', ['uses' =>'UserController@editView']);

    Route::get('users/{user}/edit/password', ['uses' =>'UserController@editPasswordView']);

    Route::get('users/{user}/remove', ['uses' =>'UserController@destroyView']);

    Route::get('TSP', 'TSPController@create');

    Route::post('TSP', 'TSPController@store');

    Route::get('parcels/order/menu', 'OrderController@show');

    Route::post('parcels/order/menu', 'OrderController@prepare');

    Route::get('parcels/order/menu/next/{courier}/{date}', 'OrderController@show2');

    Route::post('parcels/order/menu/next/{courier}/{date}', 'OrderController@prepare2');

    Route::get('parcels/order/{courier}/{date}/{localization}', ['uses' =>'OrderController@create']);

    Route::post('parcels/order/{courier}/{date}/{localization}', ['uses' =>'OrderController@store']);

    Route::get('addresses', ['uses' =>'PlaceController@index']);

    Route::get('addresses/create', 'PlaceController@create');

    Route::get('addresses/{address}/edit', ['uses' =>'PlaceController@editView']);

    Route::get('addresses/{address}/remove', ['uses' =>'PlaceController@destroyView']);

    Route::get('code/(00){code}', ['uses' =>'BarcodeController@show']);

    Route::get('parcels/{parcel}/generate_label', ['uses' =>'PDFController@generatePDF']);

    Route::get('storage/barcodes/{SSCC_number}', ['uses' =>'StorageController@showBarcode']);

    Route::get('storage/signatures/{SSCC_number}', ['uses' =>'StorageController@showSignature']);

    Route::get('download/apk', 'StorageController@downloadapk');

    Route::group(array('before'=>'csrf'), function(){

        Route::get('logout', 'SessionsController@destroy');

        Route::get('logout/{message}', ['uses' =>'SessionsController@destroyWithMessage']);

        Route::post('parcels', 'ParcelController@store');

        Route::put('parcels/{parcel}', ['uses' =>'ParcelController@edit']);

        Route::delete('parcels/{parcel}', ['uses' =>'ParcelController@destroy']);

        Route::put('users/{user}', ['uses' =>'UserController@edit']);

        Route::put('users/{user}/password', ['uses' =>'UserController@editPassword']);

        Route::delete('users/{user}', ['uses' =>'UserController@destroy']);

        Route::post('addresses', 'PlaceController@store');

        Route::put('addresses/{address}', ['uses' =>'PlaceController@edit']);

        Route::delete('addresses/{address}', ['uses' =>'PlaceController@destroy']);

        Route::put('TSP/{change}', ['uses' =>'TSPController@edit']);
    });

    Route::get('parcels/{parcel}', ['uses' =>'ParcelController@show']);

    Route::get('parcels/{parcel}/history', ['uses' =>'HistoryController@show']);

    Route::get('users/{user}', ['uses' =>'UserController@show']);

    Route::get('mail', 'MailController@create');

    //Route::group(array('before'=>'csrf'), function(){

        Route::post('mail', 'MailController@store');

    //});

});
