<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use JetBrains\PhpStorm\ArrayShape;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected string $redirectTo = '/index';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    #[ArrayShape(['old_password' => "string[]", 'new_password' => "array"])]
    protected function rules(): array
    {
        return [
            'old_password' => ['required', auth('admin')->check() ? 'password:admin' : 'password:user'],
            'new_password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ];
    }

    #[ArrayShape(['email' => "mixed|string", 'password' => "mixed", 'password_confirmation' => "mixed", 'token' => "string"])]
    protected function credentials(Request $request): array
    {
        return ['email' => Auth::user()->email, 'password' => $request->input('new_password'), 'password_confirmation' => $request->input('new_password_confirmation'), 'token' => $this->broker()->createToken(Auth::user())];
    }

    protected function broker(): PasswordBroker
    {
        return Password::broker(auth('admin')->check() ? 'admins' : null);
    }
}