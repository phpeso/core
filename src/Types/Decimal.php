<?php

declare(strict_types=1);

namespace Peso\Core\Types;

use BcMath\Number;
use Brick\Math\BigDecimal;
use Peso\Core\Helpers\Calculator;
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
     * @param numeric-string|float|Decimal|Number|BigDecimal $value
     */
    public static function init(string|float|Decimal|Number|BigDecimal $value): self
    {
        if ($value instanceof Decimal) {
            return $value;
        }
        if (\is_float($value) && $value > 0) {
            if (preg_match('/^(\d+\.?\d*)(?:E([+-]?\d+))?$/i', (string)$value, $matches)) {
                $decimal = new Decimal($matches[1]);
                if (isset($matches[2])) { // scientific notation
                    $exp = (int)$matches[2];
                    $expValue = new Decimal(match ($exp <=> 0) {
                        1, 0 => '1' . str_repeat('0', $exp),
                        -1 => '0.' . str_repeat('0', -1 - $exp) . '1'
                    });
                    $decimal = Calculator::instance()->multiply($decimal, $expValue);
                }
                return $decimal;
            }
        }

        return new Decimal((string)$value);
    }
}
