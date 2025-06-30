<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Override;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Responses\ErrorResponse;

final readonly class NullService implements PesoServiceInterface
{
    #[Override]
    public function send(object $request): ErrorResponse
    {
        return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
    }

    #[Override]
    public function supports(object $request): bool
    {
        return false;
    }
}
