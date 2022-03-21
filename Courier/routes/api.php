<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*The api.php routes are already prefixed with /api. You don't need to add this again yourself
  Route::post('api/login', ...); -> http://demotspservice.pl/api/api/login
*/

//Route::group(['prefix' => 'auth'], function () {
Route::group(array('before'=>'csrf'), function(){

    Route::post('login', 'API\ApiUserController@login');
    Route::post('register', 'API\ApiUserController@register');
});

    Route::group(['middleware' => 'auth:api'], function(){
//        Route::get('details', 'API\ApiUserController@details');
        Route::get('parcels', 'API\ApiParcelController@index');
        Route::get('parcels/{parcel}', ['uses' =>'API\ApiParcelController@show']);
        Route::get('parcels/bySSCC/{SSCC}', ['uses' =>'API\ApiParcelController@showBySSCC']);
        Route::get('code/(00){code}', ['uses' =>'API\ApiBarcodeController@show']);
        Route::post('parcels/order/by_date', ['uses' =>'API\ApiOrderController@prepare']);
        Route::post('parcels/order/by_date_and_loc', ['uses' =>'API\ApiOrderController@prepare2']);
        Route::post('TSP/order/by_date', ['uses' =>'API\ApiTSPController@prepare']);
        Route::post('TSP/order/by_date_and_loc', ['uses' =>'API\ApiTSPController@prepare2']);
        Route::post('TSP', 'API\ApiTSPController@store');

        Route::group(array('before'=>'csrf'), function(){
            Route::get('logout', 'API\ApiUserController@logout');
            Route::put('parcels/{parcel}/edit', ['uses' =>'API\ApiParcelController@edit']);
            Route::post('parcels/{parcel}/edit', ['uses' =>'API\ApiParcelController@deliver']);
        });
    });
//});
