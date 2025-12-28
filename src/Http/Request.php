<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

final class Request
{
    public ParameterBag $query;
    public ParameterBag $request;
    public ParameterBag $attributes;
    public ParameterBag $cookies;
    public FileBag $files;
    public ParameterBag $server;
    public HeaderBag $headers;
    public ?ParameterBag $session = null;

    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        array $headers = [],
        ?array $session = null
    ) {
        $this->query = new ParameterBag($query);
        $this->request = new ParameterBag($request);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new ParameterBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ParameterBag($server);
        $this->headers = new HeaderBag($headers);
        $this->session = $session !== null ? new ParameterBag($session) : null;
    }

    public static function capture(): self
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $requestData = $_POST;

        // Handle JSON Body
        if (str_contains($headers['Content-Type'] ?? '', 'application/json')) {
            $rawBody = file_get_contents('php://input');
            $json = json_decode($rawBody, true);
            if (is_array($json)) {
                $requestData = array_merge($requestData, $json);
            }
        }

        return new self(
            $_GET,
            $requestData,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER,
            $headers,
            $_SESSION ?? null
        );
    }

    /* ---------- Basic Info ---------- */

    public function method(): string
    {
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function uri(): string
    {
        return strtok($this->server->get('REQUEST_URI', '/'), '?');
    }

    public function fullUri(): string
    {
        return $this->server->get('REQUEST_URI', '/');
    }

    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers->get($key, $default);
    }

    public function ip(): ?string
    {
        return $this->server->get('REMOTE_ADDR');
    }

    public function scheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function isSecure(): bool
    {
        $https = $this->server->get('HTTPS');
        return !empty($https) && 'off' !== strtolower($https);
    }

    public function host(): string
    {
        return $this->headers->get('HOST', '');
    }

    public function port(): int
    {
        return (int) $this->server->get('SERVER_PORT', 80);
    }

    public function path(): string
    {
        return $this->uri();
    }

    public function baseUrl(): string
    {
        $scheme = $this->scheme();
        $host = $this->host();
        return "$scheme://$host";
    }

    public function isJson(): bool
    {
        return str_contains($this->header('Content-Type', ''), 'application/json');
    }

    public function wantsJson(): bool
    {
        return str_contains($this->header('Accept', ''), 'application/json');
    }

    /* ---------- Input Handling ---------- */

    public function all(): array
    {
        return array_merge($this->query->all(), $this->request->all());
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->request->get($key)
            ?? $this->query->get($key)
            ?? $default;
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->all(), array_flip($keys));
    }

    public function has(string $key): bool
    {
        return $this->request->has($key) || $this->query->has($key);
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query->get($key, $default);
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->request->get($key, $default);
    }

    /* ---------- Session & Cookies ---------- */

    public function session(string $key, mixed $default = null): mixed
    {
        return $this->session ? $this->session->get($key, $default) : $default;
    }

    public function hasSession(string $key): bool
    {
        return $this->session ? $this->session->has($key) : false;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies->get($key, $default);
    }

    /* ---------- File Handling ---------- */

    /**
     * @return UploadedFile|null
     */
    public function file(string $key): ?UploadedFile
    {
        return $this->files->get($key);
    }

    public function hasFile(string $key): bool
    {
        $file = $this->file($key);
        return $file instanceof UploadedFile && $file->isValid();
    }

    /* ---------- Attributes (ROUTER / CONTROLLER READY) ---------- */

    public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes->set($key, $value);
        return $this;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes->get($key, $default);
    }
}
