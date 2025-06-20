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
    public static function init(string|float|Decimal|Number|BigDecimal $value): self
    {
        if ($value instanceof Decimal) {
            return $value;
        }
        if (is_float($value) && $value > 0) {
            $precision = 10 + (int)(ceil(-log10($value)));
            if ($precision < 0) {
                $precision = 0;
            }
            $value = sprintf("%0.{$precision}f", $value);
            if (str_contains($value, '.')) {
                $value = rtrim($value, '0');
            }
        }

        return new Decimal((string)$value);
    }
}
