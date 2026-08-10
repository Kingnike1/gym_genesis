<?php

namespace App\Storage;

final class LocalStorage implements StorageInterface
{
    public function __construct(private ?string $root = null)
    {
        $this->root ??= dirname(__DIR__, 2) . '/storage/uploads';
    }

    public function put(string $relativePath, string $sourcePath): void
    {
        $target = $this->path($relativePath);
        $directory = dirname($target);
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new \RuntimeException('Não foi possível criar o diretório de armazenamento.');
        }
        if (!rename($sourcePath, $target) && !copy($sourcePath, $target)) {
            throw new \RuntimeException('Não foi possível armazenar o arquivo.');
        }
        @chmod($target, 0640);
    }

    public function path(string $relativePath): string
    {
        $clean = ltrim(str_replace(['..', '\\'], ['', '/'], $relativePath), '/');
        return rtrim((string) $this->root, '/') . '/' . $clean;
    }

    public function delete(string $relativePath): void
    {
        $path = $this->path($relativePath);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
