<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Payment;
use App\Events\CanceledPaymentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayBackStampCreateRequest;
use App\Repositories\PaybackStampRepository;
use App\Services\WalletService;
use App\Validators\PayBackStampValidator;
use DB;
use Illuminate\Http\JsonResponse;
use Log;
use Throwable;

/**
 * Class PayBackStampsController.
 *
 * @package namespace App\Http\Controllers\Admin;
 */
class PayBackStampsController extends Controller
{
    protected PaybackStampRepository $repository;

    protected PayBackStampValidator $validator;

    /**
     * PayBackStampsController constructor.
     *
     * @param PaybackStampRepository $repository
     * @param PayBackStampValidator $validator
     */
    public function __construct(PaybackStampRepository $repository, PayBackStampValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PayBackStampCreateRequest $request
     * @param Payment $payment
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(PayBackStampCreateRequest $request, Payment $payment): JsonResponse
    {
        try {
            DB::beginTransaction();
            app(WalletService::class)->updateBalance($payment->user_id, $payment->total_amount, 'payback', $payment->getMorphClass(), $payment->id);
            $admin_id = $this->admin->id;
            $payment_id = $payment->id;
            $this->repository->create(compact('admin_id', 'payment_id'));
            activity('abolition')->performedOn($payment)->causedBy($this->admin)->log($request->ip());
            DB::commit();

            broadcast((new CanceledPaymentEvent($payment)));
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}