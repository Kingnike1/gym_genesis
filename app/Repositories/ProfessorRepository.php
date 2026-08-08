<?php

namespace App\Repositories;

use App\Services\Database;
use App\Tenancy\AcademyContext;

class ProfessorRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('professor', 'idprofessor', true);
    }

    public function create(int $usuarioId, string $nome, string $cref, ?string $telefone = null, ?string $bio = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO professor (academia_id, usuario_id, nome, cref, telefone, bio) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([AcademyContext::id(), $usuarioId, $nome, $cref, $telefone, $bio]);
        return (int) $this->db->lastInsertId();
    }

    public function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM professor WHERE usuario_id = ? AND academia_id = ? LIMIT 1');
        $stmt->execute([$usuarioId, AcademyContext::id()]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateProfile(int $id, string $nome, string $cref, ?string $telefone, ?string $bio, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE professor SET nome = ?, cref = ?, telefone = ?, bio = ?, status = ? WHERE idprofessor = ? AND academia_id = ?');
        return $stmt->execute([$nome, $cref, $telefone, $bio, $status, $id, AcademyContext::id()]);
    }

    public function replaceUnits(int $professorId, array $unitIds): void
    {
        Database::transaction(function () use ($professorId, $unitIds): void {
            $delete = $this->db->prepare('DELETE FROM professor_unidade WHERE professor_id = ?');
            $delete->execute([$professorId]);

            $insert = $this->db->prepare('INSERT INTO professor_unidade (professor_id, unidade_id) SELECT ?, idunidade FROM unidades WHERE idunidade = ? AND academia_id = ?');
            foreach (array_unique(array_map('intval', $unitIds)) as $unitId) {
                if ($unitId > 0) {
                    $insert->execute([$professorId, $unitId, AcademyContext::id()]);
                }
            }
        });
    }

    public function replaceSpecialties(int $professorId, array $specialties): void
    {
        Database::transaction(function () use ($professorId, $specialties): void {
            $delete = $this->db->prepare('DELETE FROM professor_especialidade WHERE professor_id = ?');
            $delete->execute([$professorId]);
            $insert = $this->db->prepare('INSERT INTO professor_especialidade (professor_id, nome) VALUES (?, ?)');
            foreach (array_unique(array_filter(array_map('trim', $specialties))) as $specialty) {
                $insert->execute([$professorId, $specialty]);
            }
        });
    }

    public function linkStudent(int $professorId, int $studentId): bool
    {
        $sql = 'INSERT INTO professor_aluno (professor_id, aluno_id, ativo)
                SELECT p.idprofessor, a.idaluno, 1
                FROM professor p INNER JOIN aluno a ON a.idaluno = ?
                WHERE p.idprofessor = ? AND p.academia_id = ? AND a.academia_id = ?
                ON DUPLICATE KEY UPDATE ativo = 1';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId, $professorId, AcademyContext::id(), AcademyContext::id()]);
    }

    public function unlinkStudent(int $professorId, int $studentId): bool
    {
        $stmt = $this->db->prepare('UPDATE professor_aluno pa INNER JOIN professor p ON p.idprofessor = pa.professor_id SET pa.ativo = 0 WHERE pa.professor_id = ? AND pa.aluno_id = ? AND p.academia_id = ?');
        return $stmt->execute([$professorId, $studentId, AcademyContext::id()]);
    }

    public function students(int $professorId): array
    {
        $sql = 'SELECT a.* FROM aluno a
                INNER JOIN professor_aluno pa ON pa.aluno_id = a.idaluno AND pa.ativo = 1
                INNER JOIN professor p ON p.idprofessor = pa.professor_id
                WHERE p.idprofessor = ? AND p.academia_id = ? AND a.academia_id = ?
                ORDER BY a.nome';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$professorId, AcademyContext::id(), AcademyContext::id()]);
        return $stmt->fetchAll();
    }
}
