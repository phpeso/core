<?php

declare(strict_types=1);

namespace Peso\Core\Requests;

final readonly class CurrentExchangeRateRequest
{
    public function __construct(
        public string $baseCurrency,
        public string $quoteCurrency,
    ) {
    }
}
