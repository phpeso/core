<?php

declare(strict_types=1);

namespace Peso\Core\Requests;

use Arokettu\Date\Date;

final readonly class HistoricalExchangeRateRequest
{
    public function __construct(
        public string $fromCurrency,
        public string $toCurrency,
        public Date $date,
    ) {
    }


}
