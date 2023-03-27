<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class DepositValidator.
 *
 * @package namespace App\Validators;
 */
class DepositValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => ['status' => ['in:0'], 'amount' => ['decimal_gt:0']],
        ValidatorInterface::RULE_UPDATE => ['status' => 'in:1,-1'],
    ];
}
