<?php

declare(strict_types=1);

namespace Peso\Core\Services\SDK\HTTP;

use Error;
use Http\Discovery\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

final readonly class DiscoveredRequestFactory implements RequestFactoryInterface
{
    private RequestFactoryInterface $innerFactory;

    public function __construct()
    {
        if (!class_exists(Psr17Factory::class)) {
            // @codeCoverageIgnoreStart
            throw new Error(
                'HTTP request factory class (PSR-17) implementation not found. ' .
                'Please pass an instance of HTTP request factory manually or install php-http/discovery.'
            );
            // @codeCoverageIgnoreEnd
        }
        $this->innerFactory = new Psr17Factory();
    }

    public function createRequest(string $method, mixed $uri): RequestInterface
    {
        return $this->innerFactory->createRequest($method, $uri);
    }
}
