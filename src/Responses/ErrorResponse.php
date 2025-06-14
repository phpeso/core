<?php

declare(strict_types=1);

namespace Peso\Core\Responses;

use Peso\Core\Exceptions\PesoResponseException;

final readonly class ErrorResponse
{
    public function __construct(public PesoResponseException $exception)
    {
    }
}
