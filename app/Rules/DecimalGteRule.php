<?php

namespace App\Rules;

use Brick\Math\RoundingMode;
use Illuminate\Contracts\Validation\Rule;

class DecimalGteRule implements Rule
{
    protected string|int|float $min;
    protected int $roundingMode;
    protected int $scale;

    /**
     * Create a new rule instance.
     *
     * @param float|int|string $min
     * @param int $roundingMode
     * @param int $scale
     */
    public function __construct(float|int|string $min, int $roundingMode = RoundingMode::UNNECESSARY, int $scale = 0)
    {
        $this->min = $min;
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
        return validator([$attribute => $value], [$attribute => [new DecimalMinRule($this->min, $this->roundingMode, $this->scale)]])->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.gte.numeric', ['value' => $this->min]);
    }
}
