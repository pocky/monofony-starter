<?php

declare(strict_types=1);

namespace App\Shared\Application\Gateway\Middleware;

use App\Shared\Application\Gateway\GatewayRequest;
use App\Shared\Application\Gateway\GatewayResponse;
use Webmozart\Assert\Assert;

final class Pipe
{
    /**
     * @param array<callable> $middlewares
     */
    public function __construct(
        private array $middlewares = [],
    ) {
    }

    public function __invoke(GatewayRequest $request, ?callable $next = null): GatewayResponse
    {
        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = static fn($request) => $middleware($request, $next);
        }

        Assert::notNull($next);
        return $next($request);
    }
}
