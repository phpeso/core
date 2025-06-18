<?php

declare(strict_types=1);

namespace Peso\Core\Helpers;

use BcMath\Number;
use Peso\Core\Types\Decimal;

final class Calculator
{
    private static CalculatorInterface $instance;

    public static function instance(): CalculatorInterface
    {
        return self::$instance ??= class_exists(Number::class) ? new BcMathCalculator() : new BrickCalculator();
    }

    /**
     * @deprecated Will be removed in 1.x
     */
    public static function multiply(Decimal $x, Decimal $y): Decimal
    {
        return self::instance()->multiply($x, $y);
    }

    /**
     * @deprecated Will be removed in 1.x
     */
    public static function invert(Decimal $x): Decimal
    {
        return self::instance()->invert($x);
    }

    /**
     * @deprecated Will be removed in 1.x
     */
    public static function round(Decimal $x, int $precision): Decimal
    {
        return self::instance()->round($x, $precision);
    }
}
