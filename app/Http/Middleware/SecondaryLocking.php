<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class SecondaryLocking
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
        $lock = $request->input('_lock');
        $redis = Redis::connection()->client();
        if (!Str::isUuid($lock) || $request->session()->has($lock) || $redis->exists($lock)) {
            abort(423);
        } else {
            $request->session()->put($lock, now()->jsonSerialize());
            $redis->set($lock, $request->session()->get($lock), 'EX', ini_get('max_execution_time'));
        }

        return $next($request);
    }
}
