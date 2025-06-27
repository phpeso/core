<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Override;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Helpers\Calculator;
use Peso\Core\Requests\CurrentConversionRequest;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalConversionRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;

final readonly class ConversionService implements ExchangeRateServiceInterface
{
    public function __construct(
        private ExchangeRateServiceInterface $service,
    ) {
    }

    #[Override]
    public function send(object $request): ConversionResponse|ErrorResponse
    {
        if ($request instanceof CurrentConversionRequest) {
            $amount = $request->baseAmount;
            $subRequest = new CurrentExchangeRateRequest($request->baseCurrency, $request->quoteCurrency);
        } elseif ($request instanceof HistoricalConversionRequest) {
            $amount = $request->baseAmount;
            $subRequest = new HistoricalExchangeRateRequest(
                $request->baseCurrency,
                $request->quoteCurrency,
                $request->date,
            );
        } else {
            return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
        }

        $subResponse = $this->service->send($subRequest);

        if ($subResponse instanceof ErrorResponse) {
            return $subResponse;
        }

        return new ConversionResponse(
            Calculator::instance()->multiply($amount, $subResponse->rate),
            $subResponse->date,
        );
    }

    #[Override]
    public function supports(object $request): bool
    {
        return $request instanceof CurrentConversionRequest || $request instanceof HistoricalConversionRequest;
    }
}
