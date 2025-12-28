<?php

declare(strict_types=1);

namespace Avik\Flow;

use Avik\Seed\Contracts\ServiceProvider;

final class FlowServiceProvider implements ServiceProvider
{
    public function register(): void
    {
        // Reserved for future container bindings
    }

    public function boot(): void
    {
        // Reserved
    }
}
