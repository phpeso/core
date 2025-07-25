<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Arokettu\Date\Date;
use BcMath\Number;
use Brick\Math\BigDecimal;
use Override;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Types\Decimal;

// can't write it shorter
// phpcs:disable Generic.Files.LineLength.TooLong
final readonly class ArrayService implements PesoServiceInterface
{
    private Date $currentDate;

    /**
     * @param array<non-empty-string, array<non-empty-string, numeric-string|float|Decimal|Number|BigDecimal>> $currentRates
     * @param array<non-empty-string, array<non-empty-string, array<non-empty-string, numeric-string|float|Decimal|Number|BigDecimal>>> $historicalRates
     */
    public function __construct(
        public array $currentRates = [],
        public array $historicalRates = [],
        Date|null $currentDate = null,
    ) {
        $this->currentDate = $currentDate ?? Date::today();
    }

    #[Override]
    public function send(object $request): ExchangeRateResponse|ErrorResponse
    {
        return match (true) {
            $request instanceof CurrentExchangeRateRequest
                => isset($this->currentRates[$request->baseCurrency][$request->quoteCurrency]) ?
                    new ExchangeRateResponse(
                        Decimal::init($this->currentRates[$request->baseCurrency][$request->quoteCurrency]),
                        $this->currentDate,
                    ) :
                    new ErrorResponse(ExchangeRateNotFoundException::fromRequest($request)),
            $request instanceof HistoricalExchangeRateRequest
                => isset($this->historicalRates[$request->date->toString()][$request->baseCurrency][$request->quoteCurrency]) ?
                    new ExchangeRateResponse(
                        Decimal::init($this->historicalRates[$request->date->toString()][$request->baseCurrency][$request->quoteCurrency]),
                        $request->date,
                    ) :
                    new ErrorResponse(ExchangeRateNotFoundException::fromRequest($request)),
            default
                => new ErrorResponse(RequestNotSupportedException::fromRequest($request)),
        };
    }

    #[Override]
    public function supports(object $request): bool
    {
        return
            $request instanceof CurrentExchangeRateRequest && $this->currentRates !== [] ||
            $request instanceof HistoricalExchangeRateRequest && $this->historicalRates !== [];
    }
}
