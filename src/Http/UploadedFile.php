<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

class UploadedFile
{
    public function __construct(
        private string $path,
        private string $originalName,
        private ?string $mimeType = null,
        private ?int $size = null,
        private int $error = UPLOAD_ERR_OK
    ) {}

    public function path(): string
    {
        return $this->path;
    }

    public function originalName(): string
    {
        return $this->originalName;
    }

    public function mimeType(): ?string
    {
        return $this->mimeType;
    }

    public function size(): ?int
    {
        return $this->size;
    }

    public function error(): int
    {
        return $this->error;
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK && is_uploaded_file($this->path);
    }

    public function move(string $directory, ?string $name = null): bool
    {
        $target = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ($name ?? $this->originalName);
        return move_uploaded_file($this->path, $target);
    }
}
