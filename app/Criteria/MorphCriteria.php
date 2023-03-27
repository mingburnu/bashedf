<?php

namespace App\Criteria;

use App\Entities\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class MorphCriteria.
 *
 * @package namespace App\Criteria;
 */
class MorphCriteria implements CriteriaInterface
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Transaction|Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Transaction|Builder
     */
    public function apply($model, RepositoryInterface $repository): Transaction|Builder
    {
        $keyWord = $this->request->get('keyWord');

        if (empty($keyWord)) {
            return $model;
        } else {
            return $model->whereHasMorph('order', ['Deposit', 'Payment'], function (Builder $q) use ($keyWord) {
                $q->where('order_id', $keyWord);
            })->orWhereHasMorph('order', ['refund',], function (Builder $q) use ($keyWord) {
                $q->where('order_id', $keyWord);
            })->whereHasMorph('order', ['payback'], function (Builder $q) use ($keyWord) {
                $q->where('order_id', $keyWord);
            })->whereHasMorph('order', ['rewind'], function (Builder $q) use ($keyWord) {
                $q->where('order_id', $keyWord);
            });
        }
    }
}
