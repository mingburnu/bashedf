<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Zhuzhichao\BankCardInfo\BankCard;

class BankCardRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return BankCard::info($value)['validated'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.bank_card');
    }
}
