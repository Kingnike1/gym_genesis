<?php

namespace App\Services;

use App\Repositories\TreinoRepository;

class TreinoService
{
    private TreinoRepository $treinoRepository;

    public function __construct(TreinoRepository $treinoRepository)
    {
        $this->treinoRepository = $treinoRepository;
    }

    public function createTreino(int $alunoId, int $professorId, string $nome, string $descricao, string $dataCriacao = null): ?int
    {
        if ($dataCriacao === null) {
            $dataCriacao = date("Y-m-d H:i:s");
        }
        return $this->treinoRepository->create($alunoId, $professorId, $nome, $descricao, $dataCriacao);
    }

    public function updateTreino(int $id, string $nome, string $descricao): bool
    {
        return $this->treinoRepository->update($id, $nome, $descricao);
    }

    public function getTreinoById(int $id): ?array
    {
        return $this->treinoRepository->find($id);
    }

    public function getTreinosByAlunoId(int $alunoId): array
    {
        return $this->treinoRepository->findByAlunoId($alunoId);
    }

    public function getTreinosByProfessorId(int $professorId): array
    {
        return $this->treinoRepository->findByProfessorId($professorId);
    }

    public function deleteTreino(int $id): bool
    {
        return $this->treinoRepository->delete($id);
    }

    public function getAllTreinos(): array
    {
        return $this->treinoRepository->all();
    }
}
