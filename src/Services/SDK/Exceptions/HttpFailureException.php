<?php

declare(strict_types=1);

namespace Peso\Core\Services\SDK\Exceptions;

use Exception;
use Peso\Core\Exceptions\RuntimeException;
use PHPUnit\Event\Code\Throwable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP request failed
 */
final class HttpFailureException extends Exception implements RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public readonly RequestInterface|null $request = null,
        public readonly ResponseInterface|null $response = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(RequestInterface $request, ResponseInterface $response): self
    {
        $message = (string)$response->getBody();
        if (preg_match('//u', $message) === false) {
            $message = '<invalid UTF-8> (please inspect the $response object)';
        } elseif (strlen($message) > 500) {
            $message = substr($message, 0, 100) . '...';
        }
        return new self(\sprintf(
            'HTTP Error %s. Response is "%s"',
            (string)$response->getStatusCode(),
            $message,
        ), $response->getStatusCode(), null, $request, $response);
    }
}
