<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Types\Decimal;

final readonly class ArrayService implements CurrentExchangeRateServiceInterface, HistoricalExchangeRateServiceInterface
{
    /**
     * @param array<string, array<string, string|Decimal>> $currentRates
     * @param array<string, array<string, array<string, string|Decimal>> $historicalRates
     */
    public function __construct(
        public array $currentRates = [],
        public array $historicalRates = [],
    ) {
    }

    public function send(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): Decimal
    {
        return Decimal::init(match (true) {
            $request instanceof CurrentExchangeRateRequest
                => $this->currentRates[$request->baseCurrency][$request->quoteCurrency]
                    ?? throw new ConversionRateNotFoundException(),
            $request instanceof HistoricalExchangeRateRequest
                => $this->historicalRates[$request->date->toString()][$request->baseCurrency][$request->quoteCurrency]
                    ?? throw new ConversionRateNotFoundException(),
        });
    }

    public function supports(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): bool
    {
        return
            $request instanceof CurrentExchangeRateRequest && $this->currentRates !== [] ||
            $request instanceof HistoricalExchangeRateRequest && $this->historicalRates !== [];
    }
}
