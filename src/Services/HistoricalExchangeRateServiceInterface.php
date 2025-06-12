<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\PesoException;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Types\Decimal;

interface HistoricalExchangeRateServiceInterface
{
    /**
     * @throws PesoException
     */
    public function send(HistoricalExchangeRateRequest $request): Decimal;

    public function supports(HistoricalExchangeRateRequest $query): bool;
}
