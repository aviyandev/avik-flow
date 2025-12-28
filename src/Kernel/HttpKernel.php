<?php

declare(strict_types=1);

namespace Avik\Flow\Kernel;

use Avik\Seed\Contracts\Kernel;
use Avik\Seed\Contracts\Terminable;
use Avik\Flow\Middleware\Pipeline;
use Avik\Flow\Http\Response;

final class HttpKernel implements Kernel
{
    public function __construct(
        private Pipeline $pipeline,
        private mixed $destination
    ) {}

    public function handle(mixed $request): Response
    {
        $response = $this->pipeline->process(
            $request,
            $this->destination
        );

        return $response instanceof Response
            ? $response
            : (new Response())->content((string) $response);
    }

    public function terminate(mixed $request, mixed $response): void
    {
        if ($this->pipeline instanceof Terminable) {
            $this->pipeline->terminate($request, $response);
        }
    }
}
