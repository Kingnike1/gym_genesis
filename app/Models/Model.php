<?php

namespace App\Models;

use App\Services\Database;
use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getConnection();
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

    // Métodos abstratos para serem implementados pelas classes filhas, se necessário
    // public function create(array $data): ?int;
    // public function update(int $id, array $data): bool;
    // public function delete(int $id): bool;
}
