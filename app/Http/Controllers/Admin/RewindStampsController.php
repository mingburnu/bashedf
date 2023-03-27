<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Payment;
use App\Events\PickedBackPaymentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\RewindStampCreateRequest;
use App\Repositories\RewindStampRepository;
use App\Services\WalletService;
use App\Validators\RewindStampValidator;
use Brick\Math\BigDecimal;
use DB;
use Illuminate\Http\JsonResponse;
use Log;
use Throwable;

/**
 * Class RewindStampsController.
 *
 * @package namespace App\Http\Controllers\Admin;
 */
class RewindStampsController extends Controller
{
    protected RewindStampRepository $repository;

    protected RewindStampValidator $validator;

    /**
     * RewindStampsController constructor.
     *
     * @param RewindStampRepository $repository
     * @param RewindStampValidator $validator
     */
    public function __construct(RewindStampRepository $repository, RewindStampValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RewindStampCreateRequest $request
     * @param Payment $payment
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(RewindStampCreateRequest $request, Payment $payment): JsonResponse
    {
        try {
            DB::beginTransaction();
            $transaction_amount = BigDecimal::of($payment->total_amount)->negated()->jsonSerialize();
            app(WalletService::class)->updateBalance($payment->user_id, $transaction_amount, 'rewind', $payment->getMorphClass(), $payment->id);
            $admin_id = $this->admin->id;
            $payment_id = $payment->id;
            $this->repository->create(compact('admin_id', 'payment_id'));
            activity('reversion')->performedOn($payment)->causedBy($this->admin)->log($request->ip());
            DB::commit();

            broadcast((new PickedBackPaymentEvent($payment)));
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}