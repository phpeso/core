<?php

declare(strict_types=1);

namespace Peso\Core\Helpers;

use Brick\Math\BigDecimal;
use Peso\Core\Types\Decimal;

final readonly class Calculator
{
    public static function multiply(Decimal $x, Decimal $y): Decimal
    {
        if (extension_loaded('bcmath')) {
            return new Decimal(bcmul($x->value, $y->value));
        }

        return Decimal::init(BigDecimal::of($x->value)->multipliedBy(BigDecimal::of($y->value)));
    }

    public static function divide(Decimal $x, Decimal $y): Decimal
    {
        if (extension_loaded('bcmath')) {
            return new Decimal(bcdiv($x->value, $y->value));
        }

        return Decimal::init(BigDecimal::of($x->value)->dividedBy(BigDecimal::of($y->value)));
    }

    public static function invert(Decimal $x): Decimal
    {
        if (extension_loaded('bcmath')) {
            return new Decimal(bcdiv('1', $x->value));
        }

        return Decimal::init(BigDecimal::one()->dividedBy(BigDecimal::of($x->value)));
    }
}
