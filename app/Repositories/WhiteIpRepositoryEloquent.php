<?php

namespace App\Repositories;

use App\Validators\WhiteIpValidator;
use App\Entities\WhiteIp;

/**
 * Class WhiteIpRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WhiteIpRepositoryEloquent extends Repository implements WhiteIpRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return WhiteIp::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return WhiteIpValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = $this->model->getFillable();
    }
}
