<?php

// admin login & logout
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('admin.login')->middleware('guest:admin');
Route::post('/login', 'Auth\LoginController@login')->name('admin.authenticate')->middleware('guest:admin');
Route::post('/logout', 'Auth\LoginController@logout')->name('admin.logout')->middleware('auth:admin');

Route::group(['middleware' => ['auth:admin', '2fa'], 'as' => 'admin.'], function () {

    Route::post('/google2fa', function () {
        return redirect(route('admin.index'));
    })->name('google2fa');

    // permission : basic
    Route::get('/', 'Admin\HomeController@index')->name('index');
    Route::get('/profile', 'Admin\ProfileController@index')->name('profile.index');
    Route::put('/profile/google2fa-secret', 'Admin\ProfileController@bind')->name('profile.google2fa-secret.bind');
    Route::post('/profile/qr-code', 'Admin\ProfileController@generate')->name('profile.qr-code.generate');
    Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::resource('login-records', 'Admin\LoginRecordsController')->only(['index']);

    Route::group(['middleware' => ['can:report', 'related.admin']], function () {
        Route::resource('reports', 'Admin\ReportsController')->parameters(['reports' => 'user'])->only(['index', 'show']);
        Route::resource('statistics', 'Admin\StatisticsController')->parameters(['sums' => 'user'])->only(['index', 'show']);
    });

    Route::group(['middleware' => ['can:deposit', 'related.admin']], function () {
        Route::resource('deposits', 'Admin\DepositsController')->only(['index', 'update']);
    });

    Route::group(['middleware' => ['can:payment', 'related.admin']], function () {
        Route::patch('/payments/{payment}/task', 'Admin\PaymentsController@lock')->name('payments.task.lock');
        Route::resource('payments', 'Admin\PaymentsController')->only(['index', 'update']);
        Route::resource('payments.payback-stamps', 'Admin\PayBackStampsController')->only(['store']);
        Route::resource('payments.rewind-stamps', 'Admin\RewindStampsController')->only(['store']);
    });

    Route::group(['middleware' => ['can:new']], function () {
        Route::patch('/news/{news}/status', 'Admin\NewsController@change')->name('news.status.change');
        Route::resource('news', 'Admin\NewsController')->except('create', 'edit', 'show');
    });

    Route::group(['middleware' => ['can:bank_card']], function () {
        Route::resource('bank-cards', 'Admin\BankCardsController')->except(['show']);
    });

    Route::group(['middleware' => ['can:user']], function () {
        Route::delete('/users/{user}/google2fa-secret', 'Admin\UsersController@clear')->name('users.google2fa-secret.clear');
        Route::post('/users/{user}/api-key', 'Admin\UsersController@display')->name('users.api-key.display');
        Route::resource('users', 'Admin\UsersController')->except('show');
    });

    Route::group(['middleware' => ['can:wallet', 'related.admin']], function () {
        Route::resource('wallets', 'Admin\WalletsController')->only(['index']);
        Route::resource('users.transactions', 'Admin\TransactionsController')->only(['index']);
        Route::resource('users.account-operations', 'Admin\AccountOperationsController')->only(['store']);
        Route::resource('users.funds', 'Admin\FundsController')->only(['index', 'store', 'update'])->shallow();
    });

    Route::group(['middleware' => ['can:admin']], function () {
        Route::resource('admins', 'Admin\AdminsController')->except('show');
    });
});