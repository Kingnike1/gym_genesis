<?php

namespace App\Services;

use App\Repositories\FuncionarioRepository;

class FuncionarioService
{
    private FuncionarioRepository $funcionarioRepository;

    public function __construct(FuncionarioRepository $funcionarioRepository)
    {
        $this->funcionarioRepository = $funcionarioRepository;
    }

    public function createFuncionario(int $usuarioId, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $salario, string $cargo, string $dataAdmissao): ?int
    {
        return $this->funcionarioRepository->create($usuarioId, $nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $salario, $cargo, $dataAdmissao);
    }

    public function updateFuncionario(int $id, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $salario, string $cargo, string $dataAdmissao): bool
    {
        return $this->funcionarioRepository->update($id, $nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $salario, $cargo, $dataAdmissao);
    }

    public function getFuncionarioById(int $id): ?array
    {
        return $this->funcionarioRepository->find($id);
    }

    public function getFuncionarioByUsuarioId(int $usuarioId): ?array
    {
        return $this->funcionarioRepository->findByUsuarioId($usuarioId);
    }

    public function deleteFuncionario(int $id): bool
    {
        return $this->funcionarioRepository->delete($id);
    }

    public function getAllFuncionarios(): array
    {
        return $this->funcionarioRepository->all();
    }
}
