<?php

namespace App\Criteria;

use App\Entities\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class MerchantsByAdminCriteria.
 *
 * @package namespace App\Criteria;
 */
class MerchantsByAdminCriteria implements CriteriaInterface
{
    protected Request $request;
    protected Admin $admin;
    protected mixed $merchants;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->admin = $this->request->user();
        $this->merchants = $this->admin->merchants;
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
        return $model->whereIn('user_id', $this->merchants->modelKeys());
    }
}
