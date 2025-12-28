<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

final class Request
{
    private array $attributes = [];

    public function __construct(
        private readonly array $query,
        private readonly array $body,
        private readonly array $server,
        private readonly array $headers,
        private readonly array $cookies,
        private readonly array $files,
    ) {}

    public static function capture(): self
    {
        return new self(
            $_GET,
            $_POST,
            $_SERVER,
            function_exists('getallheaders') ? getallheaders() : [],
            $_COOKIE,
            $_FILES
        );
    }

    /* ---------- Basic Info ---------- */

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        return strtok($this->server['REQUEST_URI'] ?? '/', '?');
    }

    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key]
            ?? $this->query[$key]
            ?? $default;
    }

    /* ---------- Attributes (ROUTER / CONTROLLER READY) ---------- */

    public function setAttribute(string $key, mixed $value): self
    {
        $clone = clone $this;
        $clone->attributes[$key] = $value;
        return $clone;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
}
