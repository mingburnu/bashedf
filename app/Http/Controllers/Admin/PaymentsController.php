<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\MerchantsByAdminCriteria;
use App\Entities\Payment;
use App\Events\LockedPaymentEvent;
use App\Events\UpdatedPaymentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentUpdateRequest;
use App\Repositories\PaymentRepository;
use App\Validators\PaymentValidator;
use DataTables;
use DB;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Str;
use Throwable;

class PaymentsController extends Controller
{
    protected PaymentRepository $repository;

    protected PaymentValidator $validator;

    /**
     * PaymentsController constructor.
     *
     * @param PaymentRepository $repository
     * @param PaymentValidator $validator
     */
    public function __construct(PaymentRepository $repository, PaymentValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws RepositoryException
     */
    public function index(): View|Factory|JsonResponse|Application
    {
        if (request()->expectsJson()) {
            $qualifiedCreatedAtColumn = $this->repository->getQualifiedCreatedAtColumn();
            $search_value = $this->escapeSpecificChars(request('search.value'));

            $recordsTotal = $this->repository->pushCriteria(app(MerchantsByAdminCriteria::class))->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', $search_value);
                request()->request->set('searchFields', 'order_id:like binary;customized_id:like binary;account_name:=;account_number:=;user.name:=');
                request()->request->set('searchJoin', 'or');
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->with(['user', 'admin', 'paybackStamp', 'rewindStamp', 'transactions', 'callbackRecord'])
                ->orderBy($qualifiedCreatedAtColumn, 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        return view('admin.payments.index');
    }

    /**
     * @param PaymentUpdateRequest $request
     * @param Payment $payment
     * @return JsonResponse
     * @throws Throwable
     */
    public function lock(PaymentUpdateRequest $request, Payment $payment): JsonResponse
    {
        try {
            DB::beginTransaction();
            $payment = $this->repository->lockAndUpdate(['admin_id' => $this->admin->id], $payment->id);
            activity('lock')->performedOn($payment)->causedBy($this->admin)->log($request->ip());
            DB::commit();

            broadcast((new LockedPaymentEvent($payment, $this->admin)));
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PaymentUpdateRequest $request
     * @param Payment $payment
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(PaymentUpdateRequest $request, Payment $payment): JsonResponse
    {
        try {
            DB::beginTransaction();
            $payment = $this->repository->lockAndUpdate($request->only('status'), $payment->id);
            activity('check')->performedOn($payment)->causedBy($this->admin)->log($request->ip());
            DB::commit();

            broadcast((new UpdatedPaymentEvent($payment)));
            return response()->json();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([], 500);
        }
    }
}