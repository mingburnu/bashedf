<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class PaymentValidator.
 *
 * @package namespace App\Validators;
 */
class PaymentValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => ['status' => ['in:0'], 'amount' => ['decimal_gt:0']],
        ValidatorInterface::RULE_UPDATE => ['status' => ['in:1,-1']],
    ];
}
