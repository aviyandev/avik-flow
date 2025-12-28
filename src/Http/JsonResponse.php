<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

final class JsonResponse extends Response
{
    public function __construct(mixed $data, int $status = 200)
    {
        $this->status($status);
        $this->header('Content-Type', 'application/json');
        $this->content(json_encode($data, JSON_THROW_ON_ERROR));
    }
}
