<?php

namespace App\Storage;

interface StorageInterface
{
    public function put(string $relativePath, string $sourcePath): void;
    public function path(string $relativePath): string;
    public function delete(string $relativePath): void;
}
