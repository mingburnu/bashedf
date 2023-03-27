<?php

namespace App\Criteria;

use App\Entities\Admin;
use App\Entities\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AuthUserCriteria.
 *
 * @package namespace App\Criteria;
 */
class AuthUserCriteria implements CriteriaInterface
{
    protected ?User $clerk;
    protected ?User $merchant;
    protected ?Admin $admin;

    public function __construct()
    {
        $user = auth()->user();
        if ($user instanceof Admin) {
            $this->admin = $user;
        } else if ($user instanceof User) {
            $this->clerk = $user;
            $this->merchant = is_null($this->clerk->api_key) ? $this->clerk->node->parent->user : $this->clerk;
        }
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
        $user = auth()->user();
        if ($user instanceof Admin) {
            return $model->where($this->merchant->getForeignKey(), $this->merchant->getKey());
        } elseif ($user instanceof User) {
            return $model->where($this->merchant->getForeignKey(), $this->merchant->getKey());
        } else {
            return $model;
        }
    }
}