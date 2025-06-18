<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use BcMath\Number;
use Brick\Math\BigDecimal;
use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Exceptions\PesoResponseException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\SuccessResponse;
use Peso\Core\Types\Decimal;

// can't write it shorter
// phpcs:disable Generic.Files.LineLength.TooLong
final readonly class ArrayService implements ExchangeRateServiceInterface
{
    /**
     * @param array<non-empty-string, array<non-empty-string, numeric-string|Decimal|Number|BigDecimal>> $currentRates
     * @param array<non-empty-string, array<non-empty-string, array<non-empty-string, numeric-string|Decimal|Number|BigDecimal>> $historicalRates
     */
    public function __construct(
        public array $currentRates = [],
        public array $historicalRates = [],
    ) {
    }

    public function send(object $request): SuccessResponse|ErrorResponse
    {
        try {
            return new SuccessResponse($this->doSend($request));
        } catch (PesoResponseException $e) {
            return new ErrorResponse($e);
        }
    }

    /**
     * @throws PesoResponseException
     */
    private function doSend(object $request): Decimal
    {
        return Decimal::init(match (true) {
            $request instanceof CurrentExchangeRateRequest
                => $this->currentRates[$request->baseCurrency][$request->quoteCurrency]
                    ?? throw ConversionRateNotFoundException::fromRequest($request),
            $request instanceof HistoricalExchangeRateRequest
                => $this->historicalRates[$request->date->toString()][$request->baseCurrency][$request->quoteCurrency]
                    ?? throw ConversionRateNotFoundException::fromRequest($request),
            default
                => throw RequestNotSupportedException::fromRequest($request),
        });
    }

    public function supports(object $request): bool
    {
        return
            $request instanceof CurrentExchangeRateRequest && $this->currentRates !== [] ||
            $request instanceof HistoricalExchangeRateRequest && $this->historicalRates !== [];
    }
}
