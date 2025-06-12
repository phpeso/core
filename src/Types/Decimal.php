<?php

declare(strict_types=1);

namespace Peso\Core\Types;

use ValueError;

final readonly class Decimal
{
    public function __construct(
        public string $value,
    ) {
        if (!preg_match('/^-?\d*\.\d*$/', $value)) {
            throw new ValueError('$value must be a string representing a decimal number');
        }
    }

    public static function init(string|Decimal $value): self
    {
        if ($value instanceof Decimal) {
            return $value;
        }

        return new Decimal($value);
    }
}
