<?php

namespace App\Http\Controllers;

use App\Criteria\AuthUserCriteria;
use App\Http\Requests\PaymentCreateRequest;
use App\Repositories\PaymentRepository;
use Brick\Math\BigDecimal;
use DataTables;
use DB;
use Hashids;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use App\Validators\PaymentValidator;
use Str;
use Throwable;

/**
 * Class PaymentsController.
 *
 * @package namespace App\Http\Controllers;
 */
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
            $search_value = $this->escapeSpecificChars(request('search.value'));

            $recordsTotal = $this->repository->pushCriteria(app(AuthUserCriteria::class))->count();
            if ($recordsTotal === 0) {
                return datatables([])->toJson();
            }

            if (Str::length($search_value) === 0) {
                $recordsFiltered = $recordsTotal;
            } else {
                $this->initRepositoryParams();
                request()->request->set('search', $search_value);
                request()->request->set('searchFields', 'order_id:like binary;customized_id:like binary;account_name:=;account_number:=');
                request()->request->set('searchJoin', 'or');
                $recordsFiltered = $this->repository->pushCriteria(app(RequestCriteria::class))->count();
                if ($recordsFiltered === 0) {
                    return datatables([])->with('recordsTotal', $recordsTotal)->toJson();
                }
            }

            $source = $this->repository->with(['transactions', 'sendRecord.causer'])->orderBy('id', 'desc')->newQuery();
            return DataTables::eloquent($source)->setFilteredRecords($recordsFiltered)->with('recordsTotal', $recordsTotal)->skipTotalRecords()->toJson();
        }

        $balance = $this->merchant->wallet->balance;
        return view('payments.index')->with('balance', $balance);
    }

    /**
     * Show the form for creating the specified resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        $contract = $this->merchant->contract;
        return view('payments.create')->with(compact('contract'));
    }

    /**
     * @param PaymentCreateRequest $request
     * @return Application|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function store(PaymentCreateRequest $request): Redirector|RedirectResponse|Application
    {
        try {
            $processing_fee = $this->merchant->contract->payment_processing_fee;
            $rows = collect($request->input('payments'));
            $user_id = $this->merchant->id;
            $status = 0;
            $default_callback_url = $this->merchant->merchantSetting->default_payment_callback_url ?? null;

            DB::beginTransaction();
            foreach ($rows as $row) {
                $uuid = str_replace('-', '', Str::uuid()->jsonSerialize());
                $order_id = Hashids::encode(base_convert($uuid, 16, 10));
                $account_name = $row['account_name'];
                $customized_id = $row['customized_id'];
                $bank_name = $row['bank_name'];
                $account_number = $row['account_number'];
                $amount = $row['amount'];
                $branch = $row['branch'];
                $callback_url = Str::length($row['callback_url']) > 0 ? $row['callback_url'] : $default_callback_url;
                $total_amount = BigDecimal::of($amount)->plus($processing_fee)->jsonSerialize();

                $payment = $this->repository->create(compact('order_id', 'user_id', 'account_name', 'customized_id', 'bank_name', 'account_number', 'branch', 'amount', 'processing_fee', 'total_amount', 'callback_url', 'status'));
                activity('clerk')->performedOn($payment)->causedBy($this->clerk)->log($request->ip());
            }

            DB::commit();
            return redirect(route('payments.index'))->with('result', true);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput()->with('result', false);
        }
    }
}