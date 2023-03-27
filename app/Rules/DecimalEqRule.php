<?php

namespace App\Rules;

use App\Services\NumberService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Contracts\Validation\Rule;

class DecimalEqRule implements Rule
{
    protected string|int|float $compared_number;
    protected int $roundingMode;
    protected int $scale;

    /**
     * Create a new rule instance.
     *
     * @param float|int|string $compared_number
     * @param int $roundingMode
     * @param int $scale
     */
    public function __construct(float|int|string $compared_number, int $roundingMode = RoundingMode::UNNECESSARY, int $scale = 0)
    {
        $this->compared_number = $compared_number;
        $this->roundingMode = $roundingMode;
        $this->scale = $scale;
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
        if (!app(NumberService::class)->isNumeric($value)) {
            return false;
        }

        return BigDecimal::of($value)->isEqualTo($this->compared_number);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.decimal_eq', ['value' => $this->compared_number]);
    }
}
