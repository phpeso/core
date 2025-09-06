<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Responses;

use Arokettu\Date\Date;
use Peso\Core\Types\Decimal;

final readonly class ExchangeRateResponse
{
    public function __construct(
        public Decimal $rate,
        public Date $date,
    ) {
    }
}
