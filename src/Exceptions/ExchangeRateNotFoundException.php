<?php

declare(strict_types=1);

namespace Peso\Core\Exceptions;

use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Throwable;

/**
 * The request was understood but the specific pair was not found
 */
final class ExchangeRateNotFoundException extends PesoResponseException
{
    public static function fromRequest(
        CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request,
        Throwable|null $previous = null
    ): self {
        return new self(match (true) {
            $request instanceof CurrentExchangeRateRequest => \sprintf(
                'Unable to find exchange rate for %s/%s',
                $request->baseCurrency,
                $request->quoteCurrency,
            ),
            $request instanceof HistoricalExchangeRateRequest => \sprintf(
                'Unable to find exchange rate for %s/%s on %s',
                $request->baseCurrency,
                $request->quoteCurrency,
                $request->date->toString(),
            ),
        }, previous: $previous);
    }
}

class_exists(ConversionRateNotFoundException::class);
