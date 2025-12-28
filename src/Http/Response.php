<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

use Avik\Seed\Contracts\Renderable;

class Response implements Renderable
{
    protected int $status = 200;
    protected array $headers = [];
    protected array $cookies = [];
    protected string $content = '';

    public function status(int $code): static
    {
        $this->status = $code;
        return $this;
    }

    public function header(string $key, string $value): static
    {
        $this->headers[$key] = $value;
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

    public function render(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $key => $value) {
            header("$key: $value", true);
        }

        foreach ($this->cookies as $name => $value) {
            setcookie($name, $value);
        }

        echo $this->render();
    }
}
