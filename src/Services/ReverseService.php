<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Helpers\Calculator;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Types\Decimal;

final readonly class ReverseService implements CurrentExchangeRateServiceInterface, HistoricalExchangeRateServiceInterface
{
    public function __construct(
        private CurrentExchangeRateServiceInterface|HistoricalExchangeRateServiceInterface $service,
    ) {
    }

    public function send(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): Decimal
    {
        try {
            return $this->service->send($request);
        } catch (ConversionRateNotFoundException) {
            return Calculator::invert($this->service->send($request->invert()));
        }
    }

    public function supports(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): bool
    {
        return $this->service->supports($request) || $this->service->supports($request->invert());
    }
}
