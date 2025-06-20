<?php

declare(strict_types=1);

namespace Peso\Core\Helpers;

use BcMath\Number;

final class Calculator
{
    private static CalculatorInterface $instance;

    public static function instance(): CalculatorInterface
    {
        return self::$instance ??= class_exists(Number::class) ? new BcMathCalculator() : new BrickCalculator();
    }
}
