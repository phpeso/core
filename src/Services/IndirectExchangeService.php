<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Helpers\Calculator;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Types\Decimal;

final readonly class IndirectExchangeService implements CurrentExchangeRateServiceInterface, HistoricalExchangeRateServiceInterface
{
    public function __construct(
        private CurrentExchangeRateServiceInterface|HistoricalExchangeRateServiceInterface $service,
        private string $baseCurrency,
    ) {
    }

    public function send(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): Decimal
    {
        if ($request->baseCurrency === $this->baseCurrency || $request->quoteCurrency === $this->baseCurrency) {
            return $this->service->send($request);
        }

        $request1 = $request->withBaseCurrency($this->baseCurrency);
        $request2 = $request->withQuoteCurrency($this->baseCurrency);

        return Calculator::multiply($this->service->send($request1), $this->service->send($request2));
    }

    public function supports(CurrentExchangeRateRequest|HistoricalExchangeRateRequest $request): bool
    {
        if ($request->baseCurrency === $this->baseCurrency || $request->quoteCurrency === $this->baseCurrency) {
            return $this->service->supports($request);
        }

        $request1 = $request->withBaseCurrency($this->baseCurrency);
        $request2 = $request->withQuoteCurrency($this->baseCurrency);

        return $this->service->supports($request1) && $this->service->supports($request2);
    }
}
