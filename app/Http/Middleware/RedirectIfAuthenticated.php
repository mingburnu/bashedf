<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;
        $guard = $guards[0];

        if ($guard === 'admin' && Auth::guard($guard)->check()) {
            return redirect(route('admin.index'));
        }

        if (Auth::guard($guard)->check()) {
            return redirect(route('index'));
        }

        return $next($request);
    }
}