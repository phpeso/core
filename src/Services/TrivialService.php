<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Services;

use Arokettu\Date\Date;
use Override;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Types\Decimal;

final readonly class TrivialService implements PesoServiceInterface
{
    #[Override]
    public function send(object $request): ExchangeRateResponse|ErrorResponse
    {
        if ($request instanceof CurrentExchangeRateRequest || $request instanceof HistoricalExchangeRateRequest) {
            if ($request->baseCurrency === $request->quoteCurrency) {
                return new ExchangeRateResponse(new Decimal('1'), Date::today());
            } else {
                return new ErrorResponse(ExchangeRateNotFoundException::fromRequest($request));
            }
        }

        return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
    }

    #[Override]
    public function supports(object $request): bool
    {
        return
            ($request instanceof CurrentExchangeRateRequest || $request instanceof HistoricalExchangeRateRequest) &&
            $request->baseCurrency === $request->quoteCurrency;
    }
}
