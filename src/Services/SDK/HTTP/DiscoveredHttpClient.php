<?php

declare(strict_types=1);

namespace Peso\Core\Services\SDK\HTTP;

use Error;
use Http\Discovery\Psr18Client;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class DiscoveredHttpClient implements ClientInterface
{
    private ClientInterface $innerClient;

    public function __construct()
    {
        if (!class_exists(Psr18Client::class)) {
            // @codeCoverageIgnoreStart
            throw new Error(
                'HTTP client class (PSR-18) implementation not found. ' .
                'Please pass an instance of HTTP client manually or install php-http/discovery.',
            );
            // @codeCoverageIgnoreEnd
        }
        $this->innerClient = new Psr18Client();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->innerClient->sendRequest($request);
    }
}
