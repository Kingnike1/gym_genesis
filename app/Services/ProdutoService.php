<?php

namespace App\Services;

use App\Repositories\ProdutoRepository;

class ProdutoService
{
    private ProdutoRepository $produtoRepository;

    public function __construct(ProdutoRepository $produtoRepository)
    {
        $this->produtoRepository = $produtoRepository;
    }

    public function createProduto(string $nome, string $descricao, float $preco, int $estoque, string $categoria): ?int
    {
        return $this->produtoRepository->create($nome, $descricao, $preco, $estoque, $categoria);
    }

    public function updateProduto(int $id, string $nome, string $descricao, float $preco, int $estoque, string $categoria): bool
    {
        return $this->produtoRepository->update($id, $nome, $descricao, $preco, $estoque, $categoria);
    }

    public function getProdutoById(int $id): ?array
    {
        return $this->produtoRepository->find($id);
    }

    public function getProdutosByCategoria(string $categoria): array
    {
        return $this->produtoRepository->findByCategoria($categoria);
    }

    public function getProdutoByNome(string $nome): ?array
    {
        return $this->produtoRepository->findByNome($nome);
    }

    public function deleteProduto(int $id): bool
    {
        return $this->produtoRepository->delete($id);
    }

    public function getAllProdutos(): array
    {
        return $this->produtoRepository->all();
    }
}
