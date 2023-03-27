<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class AccountOperationValidator.
 *
 * @package namespace App\Validators;
 */
class AccountOperationValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => ['amount' => ['required']],
        ValidatorInterface::RULE_UPDATE => [],
    ];
}
