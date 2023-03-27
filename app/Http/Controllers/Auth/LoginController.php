<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected string $prefix;
    protected ?string $currentGuard;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->prefix = explode('/', is_null(request()->route()) ? '' : request()->route()->getPrefix())[0];
        $names = collect();

        collect(config('auth.guards'))->each(function ($value, $key) use ($names) {
            if ($value['driver'] === 'session') {
                $names->add($key);
            }
        })->keys()->implode(',');

        $this->middleware(function (Request $request, $next) {
            if ($this->prefix === 'admin') {
                $this->currentGuard = 'admin';
            } else {
                $this->currentGuard = 'user';
            }
            return $next($request);
        })->except(['logout']);

        $this->middleware(function (Request $request, $next) use ($names) {
            $this->currentGuard = $names->first(fn($name) => Auth::guard($name)->check());
            return $next($request);
        })->only(['logout']);

    }

    protected function guard(): Guard|Auth|StatefulGuard
    {
        return Auth::guard($this->currentGuard);
    }

    protected function redirectTo(): string
    {
        return $this->prefix === "admin" ? route('admin.index') : route('index');
    }

    /**
     * The user has logged out of the application.
     *
     * @param Request $request
     * @return Redirector|Application|RedirectResponse
     */
    protected function loggedOut(Request $request): Redirector|Application|RedirectResponse
    {
        if ($this->currentGuard === 'admin') {
            return redirect(route('admin.login'));
        } else {
            return redirect(route('login'));
        }
    }

    /**
     * Show the application's login form.
     *
     * @return Application|Factory|View
     */
    public function showLoginForm(): Factory|View|Application
    {
        if ($this->prefix === 'admin') {
            return view('auth.login', ['action' => route('admin.login')]);
        } else {
            return view('auth.login', ['action' => route('login')]);
        }
    }

    public function logout(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect($request->route()->getPrefix());
    }
}