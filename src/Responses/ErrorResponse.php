<?php

declare(strict_types=1);

namespace Peso\Core\Responses;

use Peso\Core\Exceptions\PesoException;

final readonly class ErrorResponse
{
    public function __construct(public PesoException $exception)
    {
    }
}
