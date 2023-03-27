<?php

namespace App\Repositories;

use App\Entities\MerchantSetting;
use App\Validators\MerchantSettingValidator;

/**
 * Class MerchantSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MerchantSettingRepositoryEloquent extends Repository implements MerchantSettingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return MerchantSetting::class;
    }

    /**
     * Specify Validator class name
     *
     * @return string
     */
    public function validator(): string
    {
        return MerchantSettingValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->fieldSearchable = $this->model->getFillable();
    }
}
