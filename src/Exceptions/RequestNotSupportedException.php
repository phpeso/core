<?php

namespace Peso\Core\Exceptions;

use Exception;
use Throwable;

final class RequestNotSupportedException extends Exception implements PesoException
{
    public static function fromRequest(object $request, Throwable|null $previous = null): self
    {
        return new self(\sprintf('Unrecognized request type: "%s"', get_debug_type($request)), previous: $previous);
    }
}
