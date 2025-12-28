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
        try {
            $response = $this->pipeline->process(
                $request,
                $this->destination
            );

            return $response instanceof Response
                ? $response
                : (new Response())->content((string) $response);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function handleException(\Throwable $e): Response
    {
        // Simple fallback. In a real framework, this would call an ExceptionHandler.
        $content = "An error occurred: " . $e->getMessage();
        return (new Response($content, 500));
    }

    public function terminate(mixed $request, mixed $response): void
    {
        if ($this->pipeline instanceof Terminable) {
            $this->pipeline->terminate($request, $response);
        }
    }
}
