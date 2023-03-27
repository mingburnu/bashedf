<?php

namespace App\Http\Middleware;

use App\Entities\User;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class Filter
{
    private ?User $merchant;

    /**
     * IP constructor.
     */
    public function __construct()
    {
        $this->merchant = Auth::user();
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
        if (empty(Hashids::decode($request->bearerToken())) || $this->merchant->api_key !== $request->bearerToken()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$this->merchant->merchantSetting->api_token_switch){
            return response()->json(['message' => 'Not authorized. The function is closed by merchant.'], 403);
        }

        if ($this->merchant->whiteIps()->exists() && !$this->merchant->whiteIps()->where('ip', $request->ip())->exists()){
            return response()->json(['message' => 'Not authorized. Illegal ip.'], 403);
        }

        return $next($request);
    }
}
