<?php

namespace App\Rules;

use App\Services\NumberService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Contracts\Validation\Rule;

class DecimalMaxRule implements Rule
{
    protected string|int|float $max;
    protected int $roundingMode;
    protected int $scale;

    /**
     * Create a new rule instance.
     *
     * @param float|int|string $max
     * @param int $roundingMode
     * @param int $scale
     */
    public function __construct(float|int|string $max, int $roundingMode = RoundingMode::UNNECESSARY, int $scale = 0)
    {
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

        $max = BigDecimal::of($this->max);
        return $decimal->isLessThanOrEqualTo($max);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.decimal_max', ['max' => $this->max]);
    }
}
