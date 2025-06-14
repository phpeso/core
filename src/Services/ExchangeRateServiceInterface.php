<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\PesoRuntimeException;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\SuccessResponse;

interface ExchangeRateServiceInterface
{
    /**
     * @throws PesoRuntimeException
     */
    public function send(object $request): SuccessResponse|ErrorResponse;

    public function supports(object $request): bool;
}
