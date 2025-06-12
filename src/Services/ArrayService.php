<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Types\Decimal;

final class ArrayService implements CurrentExchangeRateServiceInterface, HistoricalExchangeRateServiceInterface
{
    /**
     * @param array<string, array<string, string|Decimal>> $currentRates
     * @param array<string, array<string, array<string, string|Decimal>> $historicalRates
     */
    public function __construct(
        public readonly array $currentRates = [],
        public readonly array $historicalRates = [],
    ) {
    }

    public function send(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): Decimal
    {
        return Decimal::init(match (true) {
            $request instanceof CurrentExchangeRateRequest
                => $this->currentRates[$request->toCurrency][$request->fromCurrency]
                    ?? throw new ConversionRateNotFoundException(),
            $request instanceof HistoricalExchangeRateRequest
                => $this->historicalRates[$request->date->toString()][$request->toCurrency][$request->fromCurrency]
                    ?? throw new ConversionRateNotFoundException(),
        });
    }

    public function supports(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $query): bool
    {
        return
            $query instanceof CurrentExchangeRateRequest && $this->currentRates !== [] ||
            $query instanceof HistoricalExchangeRateRequest && $this->historicalRates !== [];
    }
}
