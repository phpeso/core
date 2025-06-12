<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Types\Decimal;

final readonly class NullService implements CurrentExchangeRateServiceInterface, HistoricalExchangeRateServiceInterface
{
    public function send(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): Decimal
    {
        throw new ConversionRateNotFoundException();
    }

    public function supports(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $query): bool
    {
        return false;
    }
}
