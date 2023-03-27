<?php

namespace App\Repositories;

use App\Entities\PaybackStamp;
use App\Validators\PayBackStampValidator;

/**
 * Class PaybackStampRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaybackStampRepositoryEloquent extends Repository implements PaybackStampRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return PaybackStamp::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return PayBackStampValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = $this->model->getFillable();
    }
}
