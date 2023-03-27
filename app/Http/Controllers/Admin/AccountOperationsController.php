<?php

namespace App\Http\Controllers\Admin;

use App\Entities\AccountOperation;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountOperationCreateRequest;
use App\Repositories\AccountOperationRepository;
use App\Services\WalletService;
use App\Validators\AccountOperationValidator;
use DB;
use Illuminate\Http\JsonResponse;
use Log;
use Throwable;

/**
 * Class AccountOperationsController.
 *
 * @package namespace App\Http\Controllers\Admin;
 */
class AccountOperationsController extends Controller
{
    protected AccountOperationRepository $repository;

    protected AccountOperationValidator $validator;

    /**
     * AccountOperationsController constructor.
     *
     * @param AccountOperationRepository $repository
     * @param AccountOperationValidator $validator
     */
    public function __construct(AccountOperationRepository $repository, AccountOperationValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AccountOperationCreateRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(AccountOperationCreateRequest $request, User $user): JsonResponse
    {
        try {
            DB::beginTransaction();
            $amount = $request->input('amount');
            $cause = $request->input('cause');
            $admin_id = $this->admin->id;
            $user_id = $user->id;

            /* @var AccountOperation $operation */
            $operation = $this->repository->create(compact('amount', 'cause', 'admin_id', 'user_id'));
            app(WalletService::class)->updateBalance($user_id, $amount, 'operation', $operation->getMorphClass(), $operation->getKey());
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