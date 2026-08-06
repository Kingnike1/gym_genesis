<?php

namespace App\Repositories;

use App\Services\Database;
use App\Tenancy\AcademyContext;
use PDO;

abstract class BaseRepository
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $academyScoped = false;

    public function __construct(string $table, string $primaryKey = 'id', bool $academyScoped = false)
    {
        $this->db = Database::getConnection();
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->academyScoped = $academyScoped;
    }

    public function all(): array
    {
        if (!$this->academyScoped) {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE academia_id = ?");
        $stmt->execute([$this->academyId()]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        if (!$this->academyScoped) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND academia_id = ?");
            $stmt->execute([$id, $this->academyId()]);
        }

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function delete(int $id): bool
    {
        if (!$this->academyScoped) {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ? AND academia_id = ?");
        return $stmt->execute([$id, $this->academyId()]);
    }

    protected function academyId(): int
    {
        return AcademyContext::id();
    }
}
