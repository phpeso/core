<?php

declare(strict_types=1);

namespace Peso\Core\Helpers;

use BcMath\Number;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;
use Peso\Core\Types\Decimal;
use RoundingMode;

final readonly class Calculator
{
    public static function multiply(Decimal $x, Decimal $y): Decimal
    {
        if (class_exists(Number::class)) {
            return new Decimal((string)(new Number($x->value) * new Number($y->value)));
        }

        return Decimal::init(BigDecimal::of($x->value)->multipliedBy(BigDecimal::of($y->value)));
    }

    public static function invert(Decimal $x): Decimal
    {
        if (class_exists(Number::class)) {
            $value = new Number($x->value);
            $scale = $value->scale;
            $value = $value->round($scale + 1); // add one digit
            $result = $value ** -1;
            if ($result->scale === $scale + 11) { // max scale (+1 + 10 from BcMath)
                $result = $result->round($scale + 10, RoundingMode::HalfEven);
            }
            return new Decimal((string)$result);
        }

        $value = BigDecimal::of($x->value);
        return Decimal::init(
            BigDecimal::one()->dividedBy(
                $value,
                $value->getScale() + 10,
                BrickRoundingMode::HALF_EVEN
            )
        );
    }
}
