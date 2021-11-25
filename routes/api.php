<?php

use Illuminate\Http\Request;
// use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route;

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

Route::post("login", "UserController@login");
Route::post("user", "UserController@store");

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function(){
    Route::apiResource("regions", "RegionController");
    Route::apiResource("commune", "CommuneController");
    Route::apiResource("customer", "CustomerController");
    Route::delete("customer/delete", "CustomerController@destroy");
    Route::post("logout", "UserController@logout");
});