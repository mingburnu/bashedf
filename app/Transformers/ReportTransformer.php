<?php

namespace App\Transformers;

use App\Criteria\DateTimeIntervalCriteria;
use App\Criteria\DepositReportCriteria;
use App\Criteria\PaymentReportCriteria;
use App\Repositories\DepositRepository;
use App\Repositories\PaymentRepository;
use League\Fractal\TransformerAbstract;
use App\Entities\User;
use Prettus\Repository\Exceptions\RepositoryException;

class ReportTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     * @throws RepositoryException
     */
    public function transform(User $user): array
    {
        $paymentRepository = app(PaymentRepository::class)
            ->pushCriteria(app(DateTimeIntervalCriteria::class))
            ->pushCriteria(new PaymentReportCriteria($user->id, 1));
        $depositRepository = app(DepositRepository::class)
            ->pushCriteria(app(DateTimeIntervalCriteria::class))
            ->pushCriteria(new DepositReportCriteria($user->id, 1));

        $paymentAmountSum = $paymentRepository->sum('amount');
        $depositAmountSum = $depositRepository->sum('amount');
        $paymentRepository->resetModel();
        $depositRepository->resetModel();
        $quantity = $paymentRepository->count() + $depositRepository->count();
        $name = e($user->name);
        $user->forceFill(compact('name', 'quantity', 'paymentAmountSum', 'depositAmountSum'));

        return $user->only(['id', 'name', 'quantity', 'paymentAmountSum', 'depositAmountSum']);
    }
}
