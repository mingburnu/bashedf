<?php

namespace App\Repositories;

use App\Entities\Payment;
use App\Validators\PaymentValidator;

/**
 * Class PaymentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentRepositoryEloquent extends Repository implements PaymentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Payment::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return PaymentValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = array_merge($this->model->getFillable(), ['user.name']);
    }

}
