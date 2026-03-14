<?php

namespace App\Services;

use App\Repositories\AlunoRepository;

class AlunoService
{
    private AlunoRepository $alunoRepository;

    public function __construct(AlunoRepository $alunoRepository)
    {
        $this->alunoRepository = $alunoRepository;
    }

    public function createAluno(int $usuarioId, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $peso, float $altura, string $objetivo): ?int
    {
        return $this->alunoRepository->create($usuarioId, $nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $peso, $altura, $objetivo);
    }

    public function updateAluno(int $id, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $peso, float $altura, string $objetivo): bool
    {
        return $this->alunoRepository->update($id, $nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $peso, $altura, $objetivo);
    }

    public function getAlunoById(int $id): ?array
    {
        return $this->alunoRepository->find($id);
    }

    public function getAlunoByUsuarioId(int $usuarioId): ?array
    {
        return $this->alunoRepository->findByUsuarioId($usuarioId);
    }

    public function deleteAluno(int $id): bool
    {
        return $this->alunoRepository->delete($id);
    }

    public function getAllAlunos(): array
    {
        return $this->alunoRepository->all();
    }
}
