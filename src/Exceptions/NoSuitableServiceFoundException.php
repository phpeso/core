<?php

declare(strict_types=1);

namespace Peso\Core\Exceptions;

/**
 * No service in the chain could handle the request
 */
final class NoSuitableServiceFoundException extends PesoResponseException
{
    /** @var list<PesoResponseException> */
    public readonly array $exceptions;

    public function __construct(PesoResponseException ...$exceptions)
    {
        $this->exceptions = $exceptions;
        parent::__construct('No service in the chain could handle the request', 0, $exceptions[0] ?? null);
    }
}
