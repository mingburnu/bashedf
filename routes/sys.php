<?php

Route::get('/ip', function () {
    return response()->json(['ip' => request()->ip()]);
})->name('sys.ip');