<?php

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

Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::get('items', 'ItemController@index');
Route::get('items/{id}', 'ItemController@show');
Route::get('bids', 'BidController@search');
Route::middleware([\App\Http\Middleware\ValidateAuthToken::class])->group(function () {
    Route::post('bids', 'BidController@store');
    Route::get('configurations/{userId}', 'ConfigurationController@show');
    Route::patch('configurations/{userId}', 'ConfigurationController@update');
    Route::get('autoBidStatus', 'AutoBidStatusController@show');
    Route::patch('autoBidStatus', 'AutoBidStatusController@update');
});
