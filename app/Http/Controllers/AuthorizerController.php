<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizerUpdateRequest;
use App\Repositories\AuthorizerRepository;
use App\Validators\AuthorizerValidator;
use DB;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Log;
use Throwable;

class AuthorizerController extends Controller
{
    protected AuthorizerRepository $repository;

    protected AuthorizerValidator $validator;

    /**
     * AuthorizerController constructor.
     *
     * @param AuthorizerRepository $repository
     * @param AuthorizerValidator $validator
     */
    public function __construct(AuthorizerRepository $repository, AuthorizerValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function edit(): Factory|View|Application
    {
        $authorizer = $this->merchant->authorizer ?? $this->repository->newModelInstance();
        return view('authorizer.edit', compact('authorizer'));
    }

    /**
     * @param AuthorizerUpdateRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(AuthorizerUpdateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->request->set('additional_parameters', json_decode($request->input('additional_parameters'), true));
            $this->repository->update($request->all(), $this->merchant->id);
            $result = true;
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $result = false;
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }

        return redirect()->back()->with('result', $result);
    }

    /**
     * @param AuthorizerUpdateRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function configure(AuthorizerUpdateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->repository->update($request->only('supervising'), $this->merchant->id);
            DB::commit();
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}