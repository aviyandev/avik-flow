<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

final class RedirectResponse extends Response
{
    public function __construct(string $url, int $status = 302, array $headers = [])
    {
        parent::__construct('', $status, $headers);
        $this->header('Location', $url);
    }

    public static function to(string $url, int $status = 302, array $headers = []): self
    {
        return new self($url, $status, $headers);
    }
}
