<?php

namespace App\Http\Controllers;

use App\Services\ClosureService;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = collect([$this->merchant])->map(app(ClosureService::class)->getSumClosure());

        $payment_amount_sum = data_get($data, '0.user_payment_amount_sum');
        $payment_processing_fee_sum = data_get($data, '0.user_payment_processing_fee_sum');
        $deposit_amount_sum = data_get($data, '0.user_deposit_amount_sum');
        $deposit_processing_fee_sum = data_get($data, '0.user_deposit_processing_fee_sum');
        return response()->json(compact('payment_amount_sum', 'payment_processing_fee_sum', 'deposit_amount_sum', 'deposit_processing_fee_sum'));
    }
}