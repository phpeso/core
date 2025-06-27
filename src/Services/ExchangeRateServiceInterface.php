<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\RuntimeException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;

interface ExchangeRateServiceInterface
{
    /**
     * @template T of object
     * @param T $request
     * @return (
     *      T is CurrentExchangeRateRequest ? ExchangeRateResponse|ErrorResponse : (
     *      T is HistoricalExchangeRateRequest ? ExchangeRateResponse|ErrorResponse :
     *      ErrorResponse
     * ))
     * @throws RuntimeException
     */
    public function send(object $request): ExchangeRateResponse|ErrorResponse;

    /**
     */
    public function supports(object $request): bool;
}
