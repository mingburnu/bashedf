<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class WalletValidator.
 *
 * @package namespace App\Validators;
 */
class WalletValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => ['balance' => ['decimal_min:0']],
        ValidatorInterface::RULE_UPDATE => ['balance' => ['decimal_min:0']],
    ];
}
