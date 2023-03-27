<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentCreateRequest;
use App\Presenters\PaymentPresenter;
use App\Repositories\PaymentRepository;
use App\Rules\SignRule;
use App\Services\SignService;
use App\Validators\PaymentValidator;
use Brick\Math\BigDecimal;
use DB;
use Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Validator\Exceptions\ValidatorException;
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
     * @return JsonResponse
     * @throws ValidationException
     */
    public function index(): JsonResponse
    {
        try {
            $rules = [
                'sign' => ['required', 'string', new SignRule(request()->all(), $this->merchant->api_key)],
                'field' => ['required', Rule::in(['order_id', 'customized_id'])],
                'order_id' => ['nullable', Rule::requiredIf(function () {
                    return request('field') === 'order_id';
                }), 'string'],
                'customized_id' => ['nullable', Rule::requiredIf(function () {
                    return request('field') === 'customized_id';
                }), 'string'],
            ];

            $this->validator->setRules($rules)->with(request()->all())->passesOrFail();

            $field = request('field');
            $value = request($field);
            $user_id = $this->merchant->id;

            $this->initRepositoryParams();
            request()->request->set('search', "user_id:$user_id;$field:$value");
            request()->request->set('searchFields', "$field:like binary;user_id:=");
            request()->request->set('searchJoin', 'and');
            $payment = $this->repository->pushCriteria(app(RequestCriteria::class))->setPresenter(PaymentPresenter::class)->first();
            return response()->json(app(SignService::class)->sign($payment, $this->merchant->api_key));
        } catch (ValidatorException $e) {
            throw ValidationException::withMessages($e->getMessageBag()->toArray());
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PaymentCreateRequest $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(PaymentCreateRequest $request): JsonResponse
    {
        try {
            $account_name = $request->input('account_name');
            $account_number = $request->input('account_number');
            $bank_name = $request->input('bank_name');
            $amount = $request->input('amount');
            $callback_url = $request->input('callback_url');
            $processing_fee = $this->merchant->contract->payment_processing_fee;

            $branch = $request->input('branch');
            $total_amount = BigDecimal::of($amount)->plus($processing_fee)->jsonSerialize();
            $uuid = str_replace('-', '', Str::uuid()->jsonSerialize());
            $order_id = Hashids::encode(base_convert($uuid, 16, 10));
            $customized_id = $request->input('customized_id');
            $user_id = $this->merchant->id;
            $status = 0;
            $callback_url = Str::length($callback_url) > 0 ? $callback_url : ($this->merchant->merchantSetting->default_payment_callback_url ?? null);

            $attributes = compact('order_id', 'account_name', 'customized_id', 'bank_name', 'account_number', 'branch', 'amount', 'processing_fee', 'total_amount', 'user_id', 'status', 'callback_url');

            DB::beginTransaction();
            $payment = $this->repository->create($attributes);
            $data = app(PaymentPresenter::class)->present($payment);
            $response = app(SignService::class)->sign($data, $this->merchant->api_key);
            activity('api')->performedOn($payment)->causedBy($this->clerk)->log($request->ip());
            DB::commit();

            return response()->json($response, 201);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}