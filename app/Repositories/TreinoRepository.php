<?php

namespace App\Repositories;

use App\Services\Database;
use App\Tenancy\AcademyContext;

class TreinoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('ficha_treino', 'idtreino', true);
    }

    public function create(int $alunoId, int $professorId, string $nome, string $descricao, string $dataInicio, ?string $dataFim = null, string $status = 'ativo', array $exercicios = []): int
    {
        return Database::transaction(function () use ($alunoId, $professorId, $nome, $descricao, $dataInicio, $dataFim, $status, $exercicios): int {
            $sql = 'INSERT INTO ficha_treino (academia_id, aluno_id, professor_id, nome, descricao, data_inicio, data_fim, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([AcademyContext::id(), $alunoId, $professorId, $nome, $descricao, $dataInicio, $dataFim, $status]);
            $id = (int) $this->db->lastInsertId();
            $this->replaceExercises($id, $exercicios);
            return $id;
        });
    }

    public function updatePlan(int $id, string $nome, string $descricao, string $dataInicio, ?string $dataFim, string $status, array $exercicios): bool
    {
        return Database::transaction(function () use ($id, $nome, $descricao, $dataInicio, $dataFim, $status, $exercicios): bool {
            $stmt = $this->db->prepare('UPDATE ficha_treino SET nome = ?, descricao = ?, data_inicio = ?, data_fim = ?, status = ?, versao = versao + 1 WHERE idtreino = ? AND academia_id = ?');
            $stmt->execute([$nome, $descricao, $dataInicio, $dataFim, $status, $id, AcademyContext::id()]);
            if ($stmt->rowCount() === 0 && $this->find($id) === null) {
                return false;
            }
            $this->replaceExercises($id, $exercicios);
            return true;
        });
    }

    public function findWithExercises(int $id): ?array
    {
        $treino = $this->find($id);
        if ($treino === null) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT i.*, e.nome AS exercicio_nome, e.grupo_muscular FROM ficha_treino_exercicio i INNER JOIN exercicio e ON e.idexercicio = i.exercicio_id WHERE i.treino_id = ? ORDER BY i.ordem');
        $stmt->execute([$id]);
        $treino['exercicios'] = $stmt->fetchAll();
        return $treino;
    }

    public function findByAlunoId(int $alunoId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ficha_treino WHERE aluno_id = ? AND academia_id = ? ORDER BY status = \'ativo\' DESC, data_inicio DESC');
        $stmt->execute([$alunoId, AcademyContext::id()]);
        return $stmt->fetchAll();
    }

    public function findByProfessorId(int $professorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ficha_treino WHERE professor_id = ? AND academia_id = ? ORDER BY updated_at DESC');
        $stmt->execute([$professorId, AcademyContext::id()]);
        return $stmt->fetchAll();
    }

    public function startExecution(int $treinoId, int $alunoId, ?string $observacoes = null): int
    {
        $treino = $this->find($treinoId);
        if ($treino === null || (int) $treino['aluno_id'] !== $alunoId || $treino['status'] !== 'ativo') {
            return 0;
        }

        $stmt = $this->db->prepare('INSERT INTO execucao_treino (academia_id, treino_id, aluno_id, iniciado_em, observacoes) VALUES (?, ?, ?, NOW(), ?)');
        $stmt->execute([AcademyContext::id(), $treinoId, $alunoId, $observacoes]);
        return (int) $this->db->lastInsertId();
    }

    public function finishExecution(int $executionId, int $alunoId, ?string $observacoes = null): bool
    {
        $stmt = $this->db->prepare('UPDATE execucao_treino SET concluido_em = NOW(), observacoes = COALESCE(?, observacoes) WHERE idexecucao = ? AND aluno_id = ? AND academia_id = ? AND concluido_em IS NULL');
        return $stmt->execute([$observacoes, $executionId, $alunoId, AcademyContext::id()]);
    }

    public function executionHistory(int $alunoId): array
    {
        $stmt = $this->db->prepare('SELECT et.*, ft.nome AS treino_nome FROM execucao_treino et INNER JOIN ficha_treino ft ON ft.idtreino = et.treino_id WHERE et.aluno_id = ? AND et.academia_id = ? ORDER BY et.iniciado_em DESC');
        $stmt->execute([$alunoId, AcademyContext::id()]);
        return $stmt->fetchAll();
    }

    private function replaceExercises(int $treinoId, array $exercicios): void
    {
        $delete = $this->db->prepare('DELETE FROM ficha_treino_exercicio WHERE treino_id = ?');
        $delete->execute([$treinoId]);

        if ($exercicios === []) {
            return;
        }

        $insert = $this->db->prepare('INSERT INTO ficha_treino_exercicio (treino_id, exercicio_id, ordem, series, repeticoes, carga, intervalo_segundos, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        foreach (array_values($exercicios) as $index => $item) {
            $insert->execute([
                $treinoId,
                (int) $item['exercicio_id'],
                (int) ($item['ordem'] ?? $index + 1),
                (int) $item['series'],
                (string) $item['repeticoes'],
                isset($item['carga']) && $item['carga'] !== '' ? (float) $item['carga'] : null,
                isset($item['intervalo_segundos']) && $item['intervalo_segundos'] !== '' ? (int) $item['intervalo_segundos'] : null,
                isset($item['observacoes']) ? trim((string) $item['observacoes']) : null,
            ]);
        }
    }
}
