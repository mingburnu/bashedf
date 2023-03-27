<?php

namespace App\Criteria;

use App\Entities\Deposit;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DepositReportCriteria.
 *
 * @package namespace App\Criteria;
 */
class DepositReportCriteria implements CriteriaInterface
{
    protected string|int $user_id;
    protected string|int $status;

    /**
     * DepositReportCriteria constructor.
     * @param int|string $user_id
     * @param int|string $status
     */
    public function __construct(int|string $user_id, int|string $status)
    {
        $this->user_id = $user_id;
        $this->status = $status;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Deposit|Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        $model = $model->whereUserId($this->user_id);
        $status = $this->status;

        return match ($status) {
            '-1', '0', '1' => $model->whereStatus($status),
            default => $model,
        };
    }
}
