<?php

namespace App\Services;

use App\Repositories\PlanoRepository;

class PlanoService
{
    private PlanoRepository $planoRepository;

    public function __construct(PlanoRepository $planoRepository)
    {
        $this->planoRepository = $planoRepository;
    }

    public function createPlano(string $tipo, float $preco, string $descricao, int $duriasDias): ?int
    {
        return $this->planoRepository->create($tipo, $preco, $descricao, $duriasDias);
    }

    public function updatePlano(int $id, string $tipo, float $preco, string $descricao, int $duriasDias): bool
    {
        return $this->planoRepository->update($id, $tipo, $preco, $descricao, $duriasDias);
    }

    public function getPlanoById(int $id): ?array
    {
        return $this->planoRepository->find($id);
    }

    public function getPlanoByTipo(string $tipo): ?array
    {
        return $this->planoRepository->findByTipo($tipo);
    }

    public function deletePlano(int $id): bool
    {
        return $this->planoRepository->delete($id);
    }

    public function getAllPlanos(): array
    {
        return $this->planoRepository->all();
    }
}
