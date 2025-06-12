<?php

declare(strict_types=1);

namespace Peso\Core\Requests;

use Arokettu\Date\Date;

final readonly class HistoricalExchangeRateRequest
{
    public function __construct(
        public string $baseCurrency,
        public string $quoteCurrency,
        public Date $date,
    ) {
    }

    public function invert(): self
    {
        return new self($this->quoteCurrency, $this->baseCurrency, $this->date);
    }
}
