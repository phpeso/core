<?php

declare(strict_types=1);

namespace Peso\Core\Exceptions;

use Peso\Core\Requests\CurrentConversionRequest;
use Peso\Core\Requests\HistoricalConversionRequest;
use Throwable;

final class ConversionNotPerformedException extends PesoResponseException
{
    public static function fromRequest(
        CurrentConversionRequest|HistoricalConversionRequest $request,
        Throwable|null $previous = null,
    ): self {
        return new self(match (true) {
            $request instanceof CurrentConversionRequest => \sprintf(
                'Unable to convert %s %s to %s',
                $request->baseAmount->value,
                $request->baseCurrency,
                $request->quoteCurrency,
            ),
            $request instanceof HistoricalConversionRequest => \sprintf(
                'Unable to convert %s %s to %s on %s',
                $request->baseAmount->value,
                $request->baseCurrency,
                $request->quoteCurrency,
                $request->date->toString(),
            ),
        }, previous: $previous);
    }
}
