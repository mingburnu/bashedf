<?php

namespace App\Http\Controllers\Admin;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\ClosureService;
use App\Validators\UserValidator;
use Brick\Math\BigDecimal;
use Illuminate\Http\JsonResponse;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class StatisticsController extends Controller
{
    protected UserRepository $repository;

    protected UserValidator $validator;

    /**
     * SumsController constructor.
     *
     * @param UserRepository $repository
     * @param UserValidator $validator
     */
    public function __construct(UserRepository $repository, UserValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(): JsonResponse
    {
        $email = $this->escapeSpecificChars(request('email'));
        $this->initRepositoryParams();
        request()->request->set('search', $email);
        request()->request->set('searchFields', 'email:=');
        $merchants = $this->repository->pushCriteria(app(RequestCriteria::class))->findWhereIn('id', $this->admin->merchants->modelKeys());

        $data = $merchants->map(app(ClosureService::class)->getSumClosure());
        $all_payment_amount_sum = BigDecimal::sum(...$data->pluck('user_payment_amount_sum')->isEmpty() ? [0] : $data->pluck('user_payment_amount_sum'))->jsonSerialize();
        $all_deposit_amount_sum = BigDecimal::sum(...$data->pluck('user_deposit_amount_sum')->isEmpty() ? [0] : $data->pluck('user_deposit_amount_sum'))->jsonSerialize();
        $all_payment_processing_fee_sum = BigDecimal::sum(...$data->pluck('user_payment_processing_fee_sum')->isEmpty() ? [0] : $data->pluck('user_payment_processing_fee_sum'))->jsonSerialize();
        $all_deposit_processing_fee_sum = BigDecimal::sum(...$data->pluck('user_deposit_processing_fee_sum')->isEmpty() ? [0] : $data->pluck('user_deposit_processing_fee_sum'))->jsonSerialize();
        $all_processing_fee_sum = BigDecimal::of($all_payment_processing_fee_sum)->plus($all_deposit_processing_fee_sum)->jsonSerialize();
        return response()->json(compact('all_payment_amount_sum', 'all_deposit_amount_sum', 'all_payment_processing_fee_sum', 'all_deposit_processing_fee_sum', 'all_processing_fee_sum'));
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        response();
        $data = collect([$user])->map(app(ClosureService::class)->getSumClosure());

        $payment_amount_sum = data_get($data, '0.user_payment_amount_sum');
        $deposit_amount_sum = data_get($data, '0.user_deposit_amount_sum');
        $payment_processing_fee_sum = data_get($data, '0.user_payment_processing_fee_sum');
        $deposit_processing_fee_sum = data_get($data, '0.user_deposit_processing_fee_sum');
        $processing_fee_sum = BigDecimal::of($payment_processing_fee_sum)->plus($deposit_processing_fee_sum)->jsonSerialize();
        return response()->json(compact('payment_amount_sum', 'deposit_amount_sum', 'payment_processing_fee_sum', 'deposit_processing_fee_sum', 'processing_fee_sum'));
    }
}