<?php

namespace App\Http\Middleware;

use App\Entities\Deposit;
use App\Entities\Fund;
use App\Entities\Payment;
use App\Entities\User;
use Auth;
use Closure;
use Illuminate\Http\Request;

class RelatedAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $parameter = collect($request->route()->parameters)->first();
        if ($parameter instanceof Fund || $parameter instanceof Deposit || $parameter instanceof Payment) {
            abort_unless(Auth::guard('admin')->user()->can('rule', [$parameter->user_id]), 403);
        }

        if ($parameter instanceof User) {
            abort_unless(Auth::guard('admin')->user()->can('rule', [$parameter]), 403);
        }

        return $next($request);
    }
}
