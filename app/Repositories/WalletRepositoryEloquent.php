<?php

namespace App\Repositories;

use App\Entities\Wallet;
use App\Validators\WalletValidator;

/**
 * Class WalletRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WalletRepositoryEloquent extends Repository implements WalletRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Wallet::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return WalletValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = array_merge($this->model->getFillable(), ['user.name', 'user.email', 'user.merchant_id']);
    }
}
