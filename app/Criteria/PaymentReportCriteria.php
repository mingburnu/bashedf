<?php

namespace App\Criteria;

use App\Entities\Payment;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PaymentReportCriteria.
 *
 * @package namespace App\Criteria;
 */
class PaymentReportCriteria implements CriteriaInterface
{
    protected string|int $user_id;
    protected string|int $status;

    /**
     * PaymentReportCriteria constructor.
     * @param string|int $user_id
     * @param string|int $status
     */
    public function __construct(string|int $user_id, string|int $status)
    {
        $this->user_id = $user_id;
        $this->status = $status;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Payment|Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        $model = $model->whereUserId($this->user_id);
        $status = $this->status;
        return match ($status) {
            '1' => $model->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereStatus(1)->whereDoesntHave('paybackStamp');
                })->orWhere(function ($q) {
                    $q->whereStatus(-1)->whereHas('rewindStamp');
                });
            }),
            '-1' => $model->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereStatus(-1)->whereDoesntHave('rewindStamp');
                })->orWhere(function ($q) {
                    $q->whereStatus(1)->whereHas('paybackStamp');
                });
            }),
            '0' => $model->whereStatus($status),
            default => $model,
        };
    }
}
