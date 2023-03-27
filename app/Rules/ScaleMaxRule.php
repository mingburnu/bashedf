<?php

namespace App\Rules;

use App\Services\NumberService;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ScaleMaxRule implements Rule
{
    protected int $max;

    /**
     * Create a new rule instance.
     *
     * @param int $max
     */
    public function __construct(int $max)
    {
        $this->max = $max;
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
        return $scale <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.scale_max', ['max' => $this->max]);
    }
}
