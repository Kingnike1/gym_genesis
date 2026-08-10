<?php

namespace App\Repositories;

class FileRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('arquivo', 'idarquivo', true);
    }

    public function create(int $usuarioId, string $disk, string $path, ?string $originalName, string $mime, int $size, string $visibility, ?string $purpose): int
    {
        $stmt = $this->db->prepare('INSERT INTO arquivo (academia_id, usuario_id, storage_disk, storage_path, original_name, mime_type, size_bytes, visibility, purpose) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$this->academyId(), $usuarioId, $disk, $path, $originalName, $mime, $size, $visibility, $purpose]);
        return (int) $this->db->lastInsertId();
    }

    public function findOwned(int $id, int $usuarioId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM arquivo WHERE idarquivo=? AND usuario_id=? AND academia_id=? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$id, $usuarioId, $this->academyId()]);
        return $stmt->fetch() ?: null;
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE arquivo SET deleted_at=CURRENT_TIMESTAMP WHERE idarquivo=? AND academia_id=?');
        return $stmt->execute([$id, $this->academyId()]);
    }
}
