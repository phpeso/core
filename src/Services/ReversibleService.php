<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Helpers\Calculator;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\SuccessResponse;

final readonly class ReversibleService implements ExchangeRateServiceInterface
{
    public function __construct(
        private ExchangeRateServiceInterface $service,
    ) {
    }

    public function send(object $request): SuccessResponse|ErrorResponse
    {
        if ($request instanceof CurrentExchangeRateRequest || $request instanceof HistoricalExchangeRateRequest) {
            $innerResult = $this->service->send($request);
            if ($innerResult instanceof SuccessResponse) {
                return $innerResult;
            }
            if ($innerResult->exception instanceof ConversionRateNotFoundException) {
                $invertedResult = $this->service->send($request->invert());
                if ($invertedResult instanceof SuccessResponse) {
                    return new SuccessResponse(
                        Calculator::instance()->invert($invertedResult->rate),
                        $invertedResult->date
                    );
                }
            }
            return $innerResult; // return the first result
        }

        return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
    }

    public function supports(object $request): bool
    {
        return
            ($request instanceof CurrentExchangeRateRequest || $request instanceof HistoricalExchangeRateRequest) &&
            $this->service->supports($request);
    }
}
