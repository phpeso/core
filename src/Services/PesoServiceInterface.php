<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\RuntimeException;
use Peso\Core\Requests\CurrentConversionRequest;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalConversionRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;

interface PesoServiceInterface
{
    /**
     * @template T of object
     * @param T $request
     * @return (
     *      T is CurrentExchangeRateRequest ? ExchangeRateResponse|ErrorResponse : (
     *      T is HistoricalExchangeRateRequest ? ExchangeRateResponse|ErrorResponse : (
     *      T is CurrentConversionRequest ? ConversionResponse|ErrorResponse : (
     *      T is HistoricalConversionRequest ? ConversionResponse|ErrorResponse : (
     *      ErrorResponse
     * )))))
     * @throws RuntimeException
     */
    public function send(object $request): ExchangeRateResponse|ConversionResponse|ErrorResponse;

    /**
     */
    public function supports(object $request): bool;
}

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
// load alias
class_exists(ExchangeRateServiceInterface::class);
