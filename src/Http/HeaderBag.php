<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

class HeaderBag extends ParameterBag
{
    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return parent::get(str_replace('_', '-', strtolower($key)), $default);
    }

    public function set(string $key, mixed $value): void
    {
        parent::set(str_replace('_', '-', strtolower($key)), $value);
    }

    public function has(string $key): bool
    {
        return parent::has(str_replace('_', '-', strtolower($key)));
    }
}
