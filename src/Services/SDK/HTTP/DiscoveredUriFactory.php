<?php

declare(strict_types=1);

namespace Peso\Core\Services\SDK\HTTP;

use Error;
use Http\Discovery\Psr17Factory;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

final readonly class DiscoveredUriFactory implements UriFactoryInterface
{
    private UriFactoryInterface $innerFactory;

    public function __construct()
    {
        if (!class_exists(Psr17Factory::class)) {
            // @codeCoverageIgnoreStart
            throw new Error(
                'HTTP URI factory class (PSR-17) implementation not found. ' .
                'Please pass an instance of HTTP URI factory manually or install php-http/discovery.'
            );
            // @codeCoverageIgnoreEnd
        }
        $this->innerFactory = new Psr17Factory();
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return $this->innerFactory->createUri($uri);
    }
}
