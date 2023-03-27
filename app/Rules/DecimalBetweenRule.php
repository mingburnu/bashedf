<?php

namespace App\Rules;

use App\Services\NumberService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Contracts\Validation\Rule;

class DecimalBetweenRule implements Rule
{
    protected string|int|float $min;
    protected string|int|float $max;
    protected int $roundingMode;
    protected int $scale;

    /**
     * Create a new rule instance.
     *
     * @param float|int|string $min
     * @param float|int|string $max
     * @param int $scale
     * @param int $roundingMode
     */
    public function __construct(float|int|string $min, float|int|string $max, int $roundingMode = RoundingMode::UNNECESSARY, int $scale = 0)
    {
        $this->min = $min;
        $this->max = $max;
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

        $decimal = $this->roundingMode === RoundingMode::UNNECESSARY ? BigDecimal::of($value) : BigDecimal::of($value)->toScale($this->scale, $this->roundingMode);

        $min = BigDecimal::of($this->min);
        $max = BigDecimal::of($this->max);
        return $decimal->isGreaterThanOrEqualTo($min) and $decimal->isLessThanOrEqualTo($max);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.decimal_between', ['min' => $this->min, 'max' => $this->max]);
    }
}
