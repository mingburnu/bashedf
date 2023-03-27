<?php

namespace App\Repositories;

use App\Entities\Deposit;
use App\Validators\DepositValidator;

/**
 * Class DepositRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DepositRepositoryEloquent extends Repository implements DepositRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Deposit::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return DepositValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = array_merge($this->model->getFillable(), ['user.name']);
    }
}
