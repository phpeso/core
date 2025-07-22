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
    /** @psalm-var Closure(object):(ExchangeRateResponse|ConversionResponse|ErrorResponse) */
    private Closure $send;
    /** @psalm-var Closure(object):bool */
    private Closure $supports;

    /**
     * @psalm-param callable(object):(ExchangeRateResponse|ConversionResponse|ErrorResponse) $send
     * @psalm-param callable(object):bool|null $supports
     */
    public function __construct(callable $send, callable|null $supports = null)
    {
        $this->send = $send(...);
        $this->supports = $supports !== null ? $supports(...) : static fn (object $_): true => true;
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
