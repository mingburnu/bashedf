<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TomcatPort
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
        abort_unless(in_array($request->getPort(), [8080, 8443]), 404);
        return $next($request);
    }
}
