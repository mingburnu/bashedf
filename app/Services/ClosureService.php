<?php

namespace App\Services;

use App\Criteria\DateTimeIntervalCriteria;
use App\Criteria\PaymentReportCriteria;
use App\Criteria\DepositReportCriteria;
use App\Entities\Payment;
use App\Entities\Deposit;
use App\Entities\User;
use App\Repositories\PaymentRepository;
use App\Repositories\DepositRepository;
use Closure;
use DateTimeZone;

class ClosureService
{
    function getSumClosure(): Closure
    {
        return function (User $user) {
            $paymentRepository = app(PaymentRepository::class)
                ->pushCriteria(app(DateTimeIntervalCriteria::class))
                ->pushCriteria(new PaymentReportCriteria($user->id, 1));
            $depositRepository = app(DepositRepository::class)
                ->pushCriteria(app(DateTimeIntervalCriteria::class))
                ->pushCriteria(new DepositReportCriteria($user->id, 1));

            $user_payment_amount_sum = $paymentRepository->sum('amount');
            $paymentRepository->resetModel();
            $user_payment_processing_fee_sum = $paymentRepository->sum('processing_fee');

            $user_deposit_amount_sum = $depositRepository->sum('amount');
            $depositRepository->resetModel();
            $user_deposit_processing_fee_sum = $depositRepository->sum('processing_fee');

            $paymentRepository->resetModel();
            $depositRepository->resetModel();
            $quantity = $paymentRepository->count() + $depositRepository->count();
            return ['email' => $user->email] + compact('quantity', 'user_payment_amount_sum', 'user_deposit_amount_sum', 'user_payment_processing_fee_sum', 'user_deposit_processing_fee_sum');
        };
    }

    function getByStatusClosure($status): Closure
    {
        return function (User $user) use ($status) {
            $deposits = app(DepositRepository::class)
                ->pushCriteria(app(DateTimeIntervalCriteria::class))
                ->pushCriteria(new DepositReportCriteria($user->id, $status))->get();
            $payments = app(PaymentRepository::class)
                ->pushCriteria(app(DateTimeIntervalCriteria::class))
                ->pushCriteria(new PaymentReportCriteria($user->id, $status))->with(['paybackStamp', 'rewindStamp'])
                ->get();

            $payments = $payments->map(function (Payment $payment) {
                if ($payment->status == 1) {
                    $payment->status = is_null($payment->paybackStamp) ? 1 : -1;
                } else if ($payment->status == -1) {
                    $payment->status = is_null($payment->rewindStamp) ? -1 : 1;
                }

                return $payment;
            });

            return collect($payments)->concat($deposits)
                ->sortBy(fn($item) => $item->created_at)->values();
        };
    }

    function getExportClosure($tz): Closure
    {
        if (!collect(DateTimeZone::listIdentifiers())->contains($tz)) {
            $tz = null;
        }

        return function ($item) use ($tz) {
            if ($item instanceof Payment) {
                return [
                    $item->user->email,
                    $item->account_name,
                    $item->created_at->tz($tz)->format('Y-m-d H:i:s'),
                    $item->order_id,
                    $item->order_name,
                    $item->total_amount,
                    $item->processing_fee,
                    $item->result
                ];
            } else if ($item instanceof Deposit) {
                return [
                    $item->user->email,
                    '',
                    $item->created_at->tz($tz)->format('Y-m-d H:i:s'),
                    $item->order_id,
                    $item->order_name,
                    $item->total_amount,
                    $item->processing_fee,
                    $item->result
                ];
            } else {
                return [];
            }
        };
    }
}