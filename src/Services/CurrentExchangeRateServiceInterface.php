<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\PesoException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Types\Decimal;

interface CurrentExchangeRateServiceInterface
{
    /**
     * @throws PesoException
     */
    public function send(CurrentExchangeRateRequest $request): Decimal;

    public function supports(CurrentExchangeRateRequest $query): bool;
}
