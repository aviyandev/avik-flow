<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

use Avik\Seed\Contracts\Renderable;

class Response implements Renderable
{
    protected int $status = 200;
    protected HeaderBag $headers;
    protected array $cookies = [];
    protected string $content = '';

    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = new HeaderBag($headers);
    }

    public static function make(string $content = '', int $status = 200, array $headers = []): static
    {
        return new static($content, $status, $headers);
    }

    public static function json(mixed $data, int $status = 200, array $headers = []): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }
        return $response;
    }

    public static function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($url, $status, $headers);
    }

    public static function plain(string $content, int $status = 200): self
    {
        return (new self($content, $status))->header('Content-Type', 'text/plain');
    }

    public static function html(string $content, int $status = 200): self
    {
        return (new self($content, $status))->header('Content-Type', 'text/html');
    }

    public function status(int $code): static
    {
        $this->status = $code;
        return $this;
    }

    public function header(string $key, string $value): static
    {
        $this->headers->set($key, $value);
        return $this;
    }

    public function headers(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }
        return $this;
    }

    public function cookie(string $name, string $value): static
    {
        $this->cookies[$name] = $value;
        return $this;
    }

    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    /* ---------- Common Response Helpers ---------- */

    public static function notFound(string $content = 'Not Found'): self
    {
        return new self($content, 404);
    }

    public static function forbidden(string $content = 'Forbidden'): self
    {
        return new self($content, 403);
    }

    public static function unauthorized(string $content = 'Unauthorized'): self
    {
        return new self($content, 401);
    }

    public static function error(string $content = 'Internal Server Error', int $status = 500): self
    {
        return new self($content, $status);
    }

    public function withSession(string $key, mixed $value): static
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$key] = $value;
        return $this;
    }

    /* ---------- Cache Control ---------- */

    public function setPublic(): static
    {
        $this->header('Cache-Control', 'public');
        return $this;
    }

    public function setPrivate(): static
    {
        $this->header('Cache-Control', 'private');
        return $this;
    }

    public function expireAt(\DateTimeInterface $date): static
    {
        $this->header('Expires', $date->format(\DateTimeInterface::RFC7231));
        return $this;
    }

    public function render(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);

            foreach ($this->headers->all() as $key => $value) {
                header("$key: $value", true);
            }

            foreach ($this->cookies as $name => $value) {
                setcookie($name, $value);
            }
        }

        echo $this->render();
    }
}
