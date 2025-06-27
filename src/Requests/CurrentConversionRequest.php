<?php

declare(strict_types=1);

namespace Peso\Core\Requests;

use Peso\Core\Types\Decimal;

final readonly class CurrentConversionRequest
{
    public function __construct(
        public Decimal $baseAmount,
        public string $baseCurrency,
        public string $quoteCurrency,
    ) {
    }
}
