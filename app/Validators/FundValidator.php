<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class FundValidator.
 *
 * @package namespace App\Validators;
 */
class FundValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => ['amount' => ['required', 'decimal_gt:0']],
        ValidatorInterface::RULE_UPDATE => [],
    ];
}
