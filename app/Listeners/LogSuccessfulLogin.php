<?php

namespace App\Listeners;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        if (!is_null(Request::route()) && Request::route()->getController()::class === LoginController::class) {
            activity('login')->causedBy($event->user)->log(Request::ip());
        }
    }
}