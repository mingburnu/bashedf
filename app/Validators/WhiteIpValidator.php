<?php

namespace App\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class WhiteIpValidator.
 *
 * @package namespace App\Validators;
 */
class WhiteIpValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => ['ip' => ['required', 'ip']],
        ValidatorInterface::RULE_UPDATE => [],
    ];
}
