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

// merchant login & logout
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('guest:user');
Route::post('/login', 'Auth\LoginController@login')->name('authenticate')->middleware('guest:user');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth:user');

Route::group(['middleware' => ['auth:user', '2fa']], function () {

    Route::post('/google2fa', function () {
        return redirect(route('index'));
    })->name('google2fa');

    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/profile', 'ProfileController@index')->name('profile.index');
    Route::post('/profile/qr-code', 'ProfileController@generate')->name('profile.qr-code.generate');
    Route::post('/profile/api-key', 'ProfileController@show')->name('profile.api-key.show');
    Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

    Route::resource('login-records', 'LoginRecordsController')->only(['index']);
    Route::resource('news', 'NewsController')->only(['index']);
    Route::resource('payments', 'PaymentsController')->only(['index', 'create']);
    Route::group(['middleware' => ['locking', '2nd.locking']], function () {
        Route::resource('payments', 'PaymentsController')->only(['store']);
    });

    Route::group(['middleware' => ['can:use']], function () {
        Route::put('/profile/google2fa-secret', 'ProfileController@bind')->name('profile.google2fa-secret.bind');
        Route::patch('/profile/default-payment-callback-url', 'ProfileController@link')->name('profile.default-payment-callback-url.link');
        Route::patch('/profile/api-token-switch', 'ProfileController@configure')->name('profile.api-token-switch.configure');
        Route::put('/profile/white-list', 'ProfileController@fill')->name('profile.white-list.fill');
        Route::get('/authorizer/edit', 'AuthorizerController@edit')->name('authorizer.edit');
        Route::put('/authorizer', 'AuthorizerController@update')->name('authorizer.update');
        Route::patch('/authorizer', 'AuthorizerController@configure')->name('authorizer.configure');

        Route::resource('statistics', 'StatisticsController')->only(['index']);
        Route::resource('reports', 'ReportsController')->only(['index']);
        Route::resource('transactions', 'TransactionsController')->only(['index']);
        Route::resource('children', 'ChildrenController')->except(['show']);
        Route::resource('deposits', 'DepositsController')->only(['index', 'create', 'store']);
    });
});