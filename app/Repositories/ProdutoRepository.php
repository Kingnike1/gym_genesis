<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class ProdutoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct("produto", "idproduto");
    }

    public function create(string $nome, string $descricao, float $preco, int $estoque, string $categoria): ?int
    {
        $sql = "INSERT INTO produto (nome, descricao, preco, estoque, categoria) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $descricao, float $preco, int $estoque, string $categoria): bool
    {
        $sql = "UPDATE produto SET nome=?, descricao=?, preco=?, estoque=?, categoria=? WHERE idproduto=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $id]);
    }

    public function findByCategoria(string $categoria): array
    {
        $stmt = $this->db->prepare("SELECT * FROM produto WHERE categoria = ?");
        $stmt->execute([$categoria]);
        return $stmt->fetchAll();
    }

    public function findByNome(string $nome): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM produto WHERE nome LIKE ?");
        $stmt->execute(["%{$nome}%"]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
