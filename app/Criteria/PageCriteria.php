<?php

namespace App\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PageCriteria.
 *
 * @package namespace App\Criteria;
 */
class PageCriteria implements CriteriaInterface
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
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        $start = ctype_digit($this->request->get('start')) ? $this->request->get('start') : 0;
        $length = ctype_digit($this->request->get('length')) ? $this->request->get('length') : 10;
        return $model->offset($start)->limit($length);
    }
}
