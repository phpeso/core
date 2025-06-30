<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Override;
use Peso\Core\Exceptions\NoSuitableServiceFoundException;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use ValueError;

final readonly class ChainService implements PesoServiceInterface
{
    /** @var list<PesoServiceInterface> */
    private array $services;

    public function __construct(
        PesoServiceInterface ...$services,
    ) {
        if ($services === []) {
            throw new ValueError('List of services must be non-empty');
        }
        $this->services = $services;
    }

    #[Override]
    public function send(object $request): ExchangeRateResponse|ConversionResponse|ErrorResponse
    {
        $errors = [];

        foreach ($this->services as $service) {
            $response = $service->send($request);
            if ($response instanceof ErrorResponse) {
                $errors[] = $response->exception;
            } else {
                return $response;
            }
        }

        return new ErrorResponse(new NoSuitableServiceFoundException(...$errors));
    }

    #[Override]
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
