<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalAccessTokenCreateRequest;
use DB;
use Illuminate\Http\JsonResponse;
use Log;
use Throwable;

class TokensController extends Controller
{
    /**
     * @param PersonalAccessTokenCreateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(PersonalAccessTokenCreateRequest $request): JsonResponse
    {
        try {
            $ip = $request->ip();
            DB::beginTransaction();
            $token = $this->merchant->createToken($ip);
            DB::commit();
            return response()->json(['token' => $token->plainTextToken], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}