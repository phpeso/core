<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Services;

use Override;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Helpers\Calculator;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;

final readonly class IndirectExchangeService implements PesoServiceInterface
{
    public function __construct(
        private PesoServiceInterface $service,
        private string $baseCurrency,
    ) {
    }

    #[Override]
    public function send(object $request): ExchangeRateResponse|ErrorResponse
    {
        if (!$request instanceof CurrentExchangeRateRequest && !$request instanceof HistoricalExchangeRateRequest) {
            return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
        }

        if ($request->baseCurrency === $this->baseCurrency || $request->quoteCurrency === $this->baseCurrency) {
            return $this->service->send($request);
        }

        $request1 = $request->withBaseCurrency($this->baseCurrency);
        $response1 = $this->service->send($request1);
        if ($response1 instanceof ErrorResponse) {
            return $response1;
        }

        $request2 = $request->withQuoteCurrency($this->baseCurrency);
        $response2 = $this->service->send($request2);
        if ($response2 instanceof ErrorResponse) {
            return $response2;
        }

        $date = $response1->date->compare($response2->date) < 0 ? $response1->date : $response2->date;

        return new ExchangeRateResponse(Calculator::instance()->multiply(
            $response1->rate,
            $response2->rate,
        ), $date);
    }

    #[Override]
    public function supports(object $request): bool
    {
        if (!$request instanceof CurrentExchangeRateRequest && !$request instanceof HistoricalExchangeRateRequest) {
            return false;
        }

        if ($request->baseCurrency === $this->baseCurrency || $request->quoteCurrency === $this->baseCurrency) {
            return $this->service->supports($request);
        }

        $request1 = $request->withBaseCurrency($this->baseCurrency);
        $request2 = $request->withQuoteCurrency($this->baseCurrency);

        return $this->service->supports($request1) && $this->service->supports($request2);
    }
}
