<?php

declare(strict_types=1);

namespace Avik\Flow\Http;

class FileBag extends ParameterBag
{
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $this->convertFile($value));
        }
    }

    private function convertFile(mixed $file): mixed
    {
        if ($file instanceof UploadedFile) {
            return $file;
        }

        if (is_array($file) && isset($file['tmp_name'])) {
            return new UploadedFile(
                $file['tmp_name'],
                $file['name'],
                $file['type'] ?? null,
                $file['size'] ?? null,
                $file['error']
            );
        }

        return $file;
    }
}
