<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Helpers;

use Peso\Core\Types\Decimal;

trait CalculatorCommonTrait
{
    public function trimZeros(Decimal $x): Decimal
    {
        if (!str_contains($x->value, '.')) {
            return $x;
        }

        $value = $x->value;
        $value = rtrim($value, '0'); // trim zeros
        $value = rtrim($value, '.'); // trim dot if all zeros removed

        return new Decimal($value);
    }
}
