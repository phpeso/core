<?php

declare(strict_types=1);

namespace Peso\Core\Helpers;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode as BrickRoundingMode;
use Peso\Core\Types\Decimal;

/**
 * @internal Services should not ask for a specific implementation
 */
final readonly class BrickCalculator implements CalculatorInterface
{
    use CalculatorCommonTrait;

    public function multiply(Decimal $x, Decimal $y): Decimal
    {
        return Decimal::init(BigDecimal::of($x->value)->multipliedBy(BigDecimal::of($y->value)));
    }

    public function divide(Decimal $x, Decimal $y): Decimal
    {
        return self::multiply($x, self::invert($y));
    }

    public function invert(Decimal $x): Decimal
    {
        $value = BigDecimal::of($x->value);
        $scale = $value->getScale();
        return Decimal::init(
            BigDecimal::one()->dividedBy(
                $value,
                $scale + 10,
                BrickRoundingMode::HALF_EVEN
            )
        );
    }

    public function round(Decimal $x, int $precision): Decimal
    {
        $value = BigDecimal::of($x->value);
        return Decimal::init($value->toScale($precision, BrickRoundingMode::HALF_EVEN));
    }
}
