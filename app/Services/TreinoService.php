<?php

namespace App\Services;

use App\Repositories\TreinoRepository;
use InvalidArgumentException;

class TreinoService
{
    public function __construct(private readonly TreinoRepository $treinoRepository)
    {
    }

    public function createTreino(int $alunoId, int $professorId, string $nome, string $descricao, ?string $dataInicio = null, ?string $dataFim = null, array $exercicios = []): int
    {
        $nome = trim($nome);
        if ($alunoId <= 0 || $professorId <= 0 || $nome === '') {
            throw new InvalidArgumentException('Aluno, professor e nome do treino são obrigatórios.');
        }

        return $this->treinoRepository->create(
            $alunoId,
            $professorId,
            $nome,
            trim($descricao),
            $dataInicio ?: date('Y-m-d'),
            $dataFim,
            'ativo',
            $this->validateExercises($exercicios),
        );
    }

    public function updateTreino(int $id, string $nome, string $descricao, ?string $dataInicio = null, ?string $dataFim = null, string $status = 'ativo', array $exercicios = []): bool
    {
        if (!in_array($status, ['rascunho', 'ativo', 'encerrado'], true)) {
            throw new InvalidArgumentException('Status de treino inválido.');
        }

        return $this->treinoRepository->updatePlan(
            $id,
            trim($nome),
            trim($descricao),
            $dataInicio ?: date('Y-m-d'),
            $dataFim,
            $status,
            $this->validateExercises($exercicios),
        );
    }

    public function getTreinoById(int $id): ?array
    {
        return $this->treinoRepository->findWithExercises($id);
    }

    public function getTreinosByAlunoId(int $alunoId): array
    {
        return $this->treinoRepository->findByAlunoId($alunoId);
    }

    public function getTreinosByProfessorId(int $professorId): array
    {
        return $this->treinoRepository->findByProfessorId($professorId);
    }

    public function startExecution(int $treinoId, int $alunoId, ?string $observacoes = null): int
    {
        return $this->treinoRepository->startExecution($treinoId, $alunoId, $observacoes);
    }

    public function finishExecution(int $executionId, int $alunoId, ?string $observacoes = null): bool
    {
        return $this->treinoRepository->finishExecution($executionId, $alunoId, $observacoes);
    }

    public function executionHistory(int $alunoId): array
    {
        return $this->treinoRepository->executionHistory($alunoId);
    }

    public function deleteTreino(int $id): bool
    {
        return $this->treinoRepository->delete($id);
    }

    public function getAllTreinos(): array
    {
        return $this->treinoRepository->all();
    }

    private function validateExercises(array $exercicios): array
    {
        foreach ($exercicios as $index => $item) {
            if ((int) ($item['exercicio_id'] ?? 0) <= 0 || (int) ($item['series'] ?? 0) <= 0 || trim((string) ($item['repeticoes'] ?? '')) === '') {
                throw new InvalidArgumentException('Exercício inválido na posição ' . ($index + 1) . '.');
            }
        }
        return $exercicios;
    }
}
