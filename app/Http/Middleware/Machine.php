<?php

namespace App\Http\Middleware;

use App\Entities\User;
use Closure;
use Illuminate\Http\Request;

class Machine
{
    private ?User $merchant;

    /**
     * Machine constructor.
     */
    public function __construct()
    {
        $this->merchant = auth()->user();
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->merchant->currentAccessToken()->name !== $request->ip()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return $next($request);
    }
}
