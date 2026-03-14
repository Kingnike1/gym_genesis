<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

abstract class BaseRepository
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct(string $table, string $primaryKey = 'id')
    {
        $this->db = Database::getConnection();
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
}
