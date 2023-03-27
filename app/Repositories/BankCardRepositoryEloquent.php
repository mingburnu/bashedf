<?php

namespace App\Repositories;

use App\Entities\BankCard;
use App\Validators\BankCardValidator;


/**
 * Class BankCardRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BankCardRepositoryEloquent extends Repository implements BankCardRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return BankCard::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return BankCardValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = $this->model->getFillable();
    }
}
