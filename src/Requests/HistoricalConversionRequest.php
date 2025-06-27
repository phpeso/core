<?php

declare(strict_types=1);

namespace Peso\Core\Requests;

use Arokettu\Date\Date;
use Peso\Core\Types\Decimal;

final readonly class HistoricalConversionRequest
{
    public function __construct(
        public Decimal $baseAmount,
        public string $baseCurrency,
        public string $quoteCurrency,
        public Date $date,
    ) {
    }
}
