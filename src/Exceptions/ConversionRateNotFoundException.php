<?php

declare(strict_types=1);

namespace Peso\Core\Exceptions;

class_alias(ExchangeRateNotFoundException::class, ConversionRateNotFoundException::class);

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
if (false) {
    /**
     * @deprecated use ExchangeRateNotFoundException
     */
    final class ConversionRateNotFoundException extends ExchangeRateNotFoundException
    {
    }
}
