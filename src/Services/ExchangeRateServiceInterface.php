<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\RuntimeException;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;

interface ExchangeRateServiceInterface
{
    /**
     * @throws RuntimeException
     */
    public function send(object $request): ExchangeRateResponse|ErrorResponse;

    /**
     */
    public function supports(object $request): bool;
}
