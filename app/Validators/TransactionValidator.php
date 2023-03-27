<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class TransactionValidator.
 *
 * @package namespace App\Validators;
 */
class TransactionValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => ['new_balance' => ['decimal_min:0']],
        ValidatorInterface::RULE_UPDATE => [],
    ];
}
