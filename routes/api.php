<?php

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

Route::group(['middleware' => ['accept:json'], 'as' => 'api.'], function () {
    Route::group(['middleware' => ['auth:api', 'filter', 'throttle:1,1']], function () {
        Route::resource('tokens', 'Api\TokensController')->only(['store']);
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::resource('payments', 'Api\PaymentsController')->only(['index', 'store']);
        Route::resource('wallets', 'Api\WalletsController')->only(['index']);
    });
});