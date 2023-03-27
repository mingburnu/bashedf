<?php

namespace App\Rules;

use App\Services\NumberService;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ScaleMinRule implements Rule
{
    protected int $min;

    /**
     * Create a new rule instance.
     *
     * @param int $min
     */
    public function __construct(int $min)
    {
        $this->min = $min;
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

        $scale = Str::length(rtrim(BigDecimal::of($value)->getFractionalPart(), '0'));
        return $scale >= $this->min;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.scale_min', ['min' => $this->min]);
    }
}
