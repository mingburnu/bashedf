<?php

namespace App\Criteria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DateTimeIntervalCriteria.
 *
 * @package namespace App\Criteria;
 */
class DateTimeIntervalCriteria implements CriteriaInterface
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Model|Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Builder|Model
     */
    public function apply($model, RepositoryInterface $repository): Model|Builder
    {
        $start_date_time = Carbon::hasFormat($this->request->get('start_date_time'), 'Y-m-d H:i:s') ?
            Carbon::parse($this->request->get('start_date_time')) : null;
        $end_date_time = Carbon::hasFormat($this->request->get('end_date_time'), 'Y-m-d H:i:s') ?
            Carbon::parse($this->request->get('end_date_time')) : null;

        if (!is_null($start_date_time)) {
            $model = $model->where('created_at', '>=', $start_date_time);
        }

        if (!is_null($end_date_time)) {
            $model = $model->where('created_at', '<', $end_date_time);
        }

        return $model;
    }
}
