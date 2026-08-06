<?php

namespace App\Repositories;

class TreinoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('treino', 'idtreino', true);
    }

    public function create(int $alunoId, int $professorId, string $nome, string $descricao, string $dataCriacao): ?int
    {
        $sql = 'INSERT INTO treino (aluno_id, professor_id, nome, descricao, data_criacao, academia_id) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$alunoId, $professorId, $nome, $descricao, $dataCriacao, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $descricao): bool
    {
        $sql = 'UPDATE treino SET nome=?, descricao=? WHERE idtreino=? AND academia_id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $descricao, $id, $this->academyId()]);
    }

    public function findByAlunoId(int $alunoId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM treino WHERE aluno_id = ? AND academia_id = ?');
        $stmt->execute([$alunoId, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findByProfessorId(int $professorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM treino WHERE professor_id = ? AND academia_id = ?');
        $stmt->execute([$professorId, $this->academyId()]);
        return $stmt->fetchAll();
    }
}
