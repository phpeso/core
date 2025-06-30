<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Override;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Helpers\Calculator;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;

final readonly class ReversibleService implements PesoServiceInterface
{
    public function __construct(
        private PesoServiceInterface $service,
    ) {
    }

    #[Override]
    public function send(object $request): ExchangeRateResponse|ErrorResponse
    {
        if ($request instanceof CurrentExchangeRateRequest || $request instanceof HistoricalExchangeRateRequest) {
            $innerResult = $this->service->send($request);
            if ($innerResult instanceof ExchangeRateResponse) {
                return $innerResult;
            }
            if ($innerResult->exception instanceof ExchangeRateNotFoundException) {
                $invertedResult = $this->service->send($request->invert());
                if ($invertedResult instanceof ExchangeRateResponse) {
                    return new ExchangeRateResponse(
                        Calculator::instance()->invert($invertedResult->rate),
                        $invertedResult->date,
                    );
                }
            }
            return $innerResult; // return the first result
        }

        return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
    }

    #[Override]
    public function supports(object $request): bool
    {
        return
            ($request instanceof CurrentExchangeRateRequest || $request instanceof HistoricalExchangeRateRequest) &&
            $this->service->supports($request);
    }
}
