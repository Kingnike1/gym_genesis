<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class DietaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct("dieta", "iddieta");
    }

    public function create(int $alunoId, int $professorId, string $nome, string $descricao, string $dataCriacao): ?int
    {
        $sql = "INSERT INTO dieta (aluno_id, professor_id, nome, descricao, data_criacao) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$alunoId, $professorId, $nome, $descricao, $dataCriacao]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $descricao): bool
    {
        $sql = "UPDATE dieta SET nome=?, descricao=? WHERE iddieta=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $descricao, $id]);
    }

    public function findByAlunoId(int $alunoId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM dieta WHERE aluno_id = ?");
        $stmt->execute([$alunoId]);
        return $stmt->fetchAll();
    }

    public function findByProfessorId(int $professorId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM dieta WHERE professor_id = ?");
        $stmt->execute([$professorId]);
        return $stmt->fetchAll();
    }
}
