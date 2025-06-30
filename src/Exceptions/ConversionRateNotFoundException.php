<?php

declare(strict_types=1);

namespace Peso\Core\Exceptions;

class_alias(ExchangeRateNotFoundException::class, ConversionRateNotFoundException::class);

if (false) {
    /**
     * @deprecated use ExchangeRateNotFoundException
     */
    final class ConversionRateNotFoundException extends ExchangeRateNotFoundException
    {
    }
}
