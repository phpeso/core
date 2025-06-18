<?php

declare(strict_types=1);

namespace Peso\Core\Types;

use BcMath\Number;
use Brick\Math\BigDecimal;
use ValueError;

final readonly class Decimal
{
    /** @var numeric-string */
    public string $value;

    /**
     * @param numeric-string $value
     */
    public function __construct(string $value)
    {
        if (!preg_match('/^(?:\d+\.?\d*|\d*\.?\d+)$/', $value) || preg_match('/^[.0]+$/', $value)) {
            throw new ValueError('$value must be a string representing a positive decimal number');
        }
        $this->value = $value;
    }

    /**
     * @param numeric-string|Decimal|Number|BigDecimal $value
     */
    public static function init(string|Decimal|Number|BigDecimal $value): self
    {
        if ($value instanceof Decimal) {
            return $value;
        }

        return new Decimal((string)$value);
    }
}
