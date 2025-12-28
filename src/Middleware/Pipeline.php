<?php

declare(strict_types=1);

namespace Avik\Flow\Middleware;

use Avik\Seed\Contracts\Middleware;
use Closure;

final class Pipeline
{
    public function __construct(
        private array $middlewares = []
    ) {}

    public function process(mixed $request, Closure $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function ($request) use ($middleware, $next) {
                    if ($middleware instanceof Middleware) {
                        return $middleware->handle($request, $next);
                    }

                    if (is_callable($middleware)) {
                        return $middleware($request, $next);
                    }

                    throw new \RuntimeException('Invalid middleware');
                };
            },
            $destination
        );

        return $pipeline($request);
    }
}
