<?php

namespace App\Services;

use App\Repositories\DietaRepository;

class DietaService
{
    private DietaRepository $dietaRepository;

    public function __construct(DietaRepository $dietaRepository)
    {
        $this->dietaRepository = $dietaRepository;
    }

    public function createDieta(int $alunoId, int $professorId, string $nome, string $descricao, string $dataCriacao = null): ?int
    {
        if ($dataCriacao === null) {
            $dataCriacao = date("Y-m-d H:i:s");
        }
        return $this->dietaRepository->create($alunoId, $professorId, $nome, $descricao, $dataCriacao);
    }

    public function updateDieta(int $id, string $nome, string $descricao): bool
    {
        return $this->dietaRepository->update($id, $nome, $descricao);
    }

    public function getDietaById(int $id): ?array
    {
        return $this->dietaRepository->find($id);
    }

    public function getDietasByAlunoId(int $alunoId): array
    {
        return $this->dietaRepository->findByAlunoId($alunoId);
    }

    public function getDietasByProfessorId(int $professorId): array
    {
        return $this->dietaRepository->findByProfessorId($professorId);
    }

    public function deleteDieta(int $id): bool
    {
        return $this->dietaRepository->delete($id);
    }

    public function getAllDietas(): array
    {
        return $this->dietaRepository->all();
    }
}
