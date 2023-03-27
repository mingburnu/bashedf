<?php

namespace App\Repositories;

use App\Entities\AccountOperation;
use App\Validators\AccountOperationValidator;

/**
 * Class AccountOperationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AccountOperationRepositoryEloquent extends Repository implements AccountOperationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return AccountOperation::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return AccountOperationValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = $this->model->getFillable();
    }
}
