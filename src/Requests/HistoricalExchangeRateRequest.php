<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

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

    public function withBaseCurrency(string $baseCurrency): self
    {
        return new self($baseCurrency, $this->quoteCurrency, $this->date);
    }

    public function withQuoteCurrency(string $quoteCurrency): self
    {
        return new self($this->baseCurrency, $quoteCurrency, $this->date);
    }

    public function withDate(Date $date): self
    {
        return new self($this->baseCurrency, $this->quoteCurrency, $date);
    }
}
