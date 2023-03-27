<?php

namespace App\Repositories;

use App\Entities\RewindStamp;
use App\Validators\RewindStampValidator;

/**
 * Class RewindStampRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RewindStampRepositoryEloquent extends Repository implements RewindStampRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return RewindStamp::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return RewindStampValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = $this->model->getFillable();
    }
}
