<?php

namespace App\Services;

use App\Repositories\FileRepository;
use App\Storage\StorageInterface;

final class FileService
{
    private const MAX_BYTES = 5_242_880;
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];

    public function __construct(private FileRepository $files, private StorageInterface $storage)
    {
    }

    public function storeUploadedFile(array $upload, int $usuarioId, ?string $purpose = null, string $visibility = 'private'): int
    {
        $tmp = (string) ($upload['tmp_name'] ?? '');
        $size = (int) ($upload['size'] ?? 0);
        if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_file($tmp)) {
            throw new \InvalidArgumentException('Upload inválido.');
        }
        if ($size <= 0 || $size > self::MAX_BYTES) {
            throw new \InvalidArgumentException('Arquivo excede o limite permitido.');
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmp);
        if (!isset(self::ALLOWED[$mime])) {
            throw new \InvalidArgumentException('Tipo de arquivo não permitido.');
        }
        if (!in_array($visibility, ['private','public'], true)) {
            throw new \InvalidArgumentException('Visibilidade inválida.');
        }
        $extension = self::ALLOWED[$mime];
        $relativePath = date('Y/m') . '/' . bin2hex(random_bytes(24)) . '.' . $extension;
        $this->storage->put($relativePath, $tmp);
        try {
            return $this->files->create($usuarioId, 'local', $relativePath, basename((string) ($upload['name'] ?? '')), $mime, $size, $visibility, $purpose);
        } catch (\Throwable $e) {
            $this->storage->delete($relativePath);
            throw $e;
        }
    }

    public function resolvePrivatePath(int $fileId, int $usuarioId): string
    {
        $file = $this->files->findOwned($fileId, $usuarioId);
        if (!$file || $file['visibility'] !== 'private') {
            throw new \RuntimeException('Arquivo não encontrado ou não autorizado.');
        }
        return $this->storage->path((string) $file['storage_path']);
    }
}
