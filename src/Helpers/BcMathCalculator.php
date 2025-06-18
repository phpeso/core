<?php

declare(strict_types=1);

namespace Peso\Core\Helpers;

use BcMath\Number;
use Peso\Core\Types\Decimal;
use RoundingMode;

/**
 * @internal Services should not ask for a specific implementation
 */
final readonly class BcMathCalculator implements CalculatorInterface
{
    public function multiply(Decimal $x, Decimal $y): Decimal
    {
        return Decimal::init(new Number($x->value) * new Number($y->value));
    }

    public function divide(Decimal $x, Decimal $y): Decimal
    {
        return self::multiply($x, self::invert($y));
    }

    public function invert(Decimal $x): Decimal
    {
        $value = new Number($x->value);
        $scale = $value->scale;
        $value = $value->round($scale + 2); // add two digits
        $result = $value ** -1;
        $result = $result->round($scale + 10, RoundingMode::HalfEven);
        return Decimal::init($result);
    }

    public function round(Decimal $x, int $precision): Decimal
    {
        $number = new Number($x->value);
        return new Decimal((string)$number->round($precision, RoundingMode::HalfEven));
    }
}
