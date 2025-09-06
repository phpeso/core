<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Tests\Helpers;

use Peso\Core\Helpers\BrickCalculator;
use Peso\Core\Helpers\CalculatorInterface;

final class BrickCalculatorTest extends CalculatorTestTemplate
{
    protected function getCalculator(): CalculatorInterface
    {
        return new BrickCalculator();
    }
}
