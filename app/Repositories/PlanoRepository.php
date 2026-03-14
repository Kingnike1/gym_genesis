<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class PlanoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct("plano", "idplano");
    }

    public function create(string $tipo, float $preco, string $descricao, int $duriasDias): ?int
    {
        $sql = "INSERT INTO plano (tipo, preco, descricao, duriasDias) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipo, $preco, $descricao, $duriasDias]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $tipo, float $preco, string $descricao, int $duriasDias): bool
    {
        $sql = "UPDATE plano SET tipo=?, preco=?, descricao=?, duriasDias=? WHERE idplano=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$tipo, $preco, $descricao, $duriasDias, $id]);
    }

    public function findByTipo(string $tipo): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM plano WHERE tipo = ?");
        $stmt->execute([$tipo]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
