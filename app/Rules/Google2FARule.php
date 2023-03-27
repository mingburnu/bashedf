<?php

namespace App\Rules;

use Exception;
use Google2FA;
use Illuminate\Contracts\Validation\Rule;

class Google2FARule implements Rule
{
    protected ?string $google2faSecret;

    /**
     * Create a new rule instance.
     *
     * @param $google2faSecret
     */
    public function __construct($google2faSecret)
    {
        $this->google2faSecret = $google2faSecret;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        try {
            return Google2FA::verifyKey($this->google2faSecret, $value);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.google2fa');
    }
}
