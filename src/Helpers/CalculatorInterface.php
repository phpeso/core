<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Helpers;

use Peso\Core\Types\Decimal;

interface CalculatorInterface
{
    public function multiply(Decimal $x, Decimal $y): Decimal;
    public function divide(Decimal $x, Decimal $y): Decimal;
    public function invert(Decimal $x): Decimal;
    public function round(Decimal $x, int $precision): Decimal;
    public function trimZeros(Decimal $x): Decimal;
}
