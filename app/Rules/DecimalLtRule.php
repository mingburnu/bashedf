<?php

namespace App\Rules;

use App\Services\NumberService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Contracts\Validation\Rule;

class DecimalLtRule implements Rule
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

        $decimal = $this->roundingMode === RoundingMode::UNNECESSARY ? BigDecimal::of($value) : BigDecimal::of($value)->toScale($this->scale, $this->roundingMode);

        $compared_number = BigDecimal::of($this->compared_number);
        return $decimal->isLessThan($compared_number);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.lt.numeric', ['value' => $this->compared_number]);
    }
}
