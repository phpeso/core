<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Closure;
use Override;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;

final readonly class CallbackService implements PesoServiceInterface
{
    private Closure $send;
    private Closure $supports;

    public function __construct(callable $send, callable|null $supports = null)
    {
        $this->send = Closure::fromCallable($send);
        $this->supports = $supports !== null ? Closure::fromCallable($supports) : static fn (object $_) => true;
    }

    #[Override]
    public function send(object $request): ExchangeRateResponse|ConversionResponse|ErrorResponse
    {
        return ($this->send)($request);
    }

    #[Override]
    public function supports(object $request): bool
    {
        return ($this->supports)($request);
    }
}
