<?php

namespace App\Repositories;

use App\Entities\Fund;
use App\Validators\FundValidator;

/**
 * Class FundRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FundRepositoryEloquent extends Repository implements FundRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Fund::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return FundValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = $this->model->getFillable();
    }
}
