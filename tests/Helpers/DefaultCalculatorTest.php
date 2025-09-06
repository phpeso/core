<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Tests\Helpers;

use Peso\Core\Helpers\BcMathCalculator;
use Peso\Core\Helpers\BrickCalculator;
use Peso\Core\Helpers\Calculator;
use Peso\Core\Helpers\CalculatorInterface;
use PHPUnit\Framework\TestCase;

final class DefaultCalculatorTest extends TestCase
{
    public function testInstance(): void
    {
        $calc = Calculator::instance();

        self::assertInstanceOf(CalculatorInterface::class, $calc);
        if (\extension_loaded('bcmath') && PHP_VERSION_ID >= 80400) {
            self::assertInstanceOf(BcMathCalculator::class, $calc);
        } else {
            self::assertInstanceOf(BrickCalculator::class, $calc);
        }
    }
}
