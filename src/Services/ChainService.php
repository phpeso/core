<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\NoSuitableServiceFoundException;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\SuccessResponse;
use ValueError;

final readonly class ChainService implements ExchangeRateServiceInterface
{
    /** @var list<ExchangeRateServiceInterface> */
    private array $services;

    public function __construct(
        ExchangeRateServiceInterface ...$services
    ) {
        if ($services === []) {
            throw new ValueError('List of services must be non-empty');
        }
        $this->services = $services;
    }

    public function send(object $request): SuccessResponse|ErrorResponse
    {
        $errors = [];

        foreach ($this->services as $service) {
            $response = $service->send($request);
            if ($response instanceof SuccessResponse) {
                return $response;
            }
            $errors[] = $response->exception;
        }

        return new ErrorResponse(new NoSuitableServiceFoundException(...$errors));
    }

    public function supports(object $request): bool
    {
        foreach ($this->services as $service) {
            if ($service->supports($request)) {
                return true;
            }
        }

        return false;
    }
}
