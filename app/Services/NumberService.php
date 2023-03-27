<?php

namespace App\Services;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\NumberFormatException;

class NumberService
{
    function isNumeric($n): bool
    {
        try {
            BigDecimal::of($n);
            return true;
        } catch (NumberFormatException) {
            return false;
        }
    }
}