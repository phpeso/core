<?php

declare(strict_types=1);

namespace Peso\Core\Exceptions;

use Exception;

class NoSuitableServiceFoundException extends Exception implements PesoException
{
    /** @var list<PesoException> */
    public readonly array $exceptions;

    public function __construct(PesoException ...$exceptions) {
        $this->exceptions = $exceptions;
        parent::__construct('No service in the chain could handle the request', 0, $exceptions[0] ?? null);
    }
}
