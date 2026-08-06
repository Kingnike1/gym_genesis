<?php

namespace App\Repositories;

class ProdutoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('produto', 'idproduto', true);
    }

    public function create(string $nome, string $descricao, float $preco, int $estoque, string $categoria): ?int
    {
        $sql = 'INSERT INTO produto (nome, descricao, preco, estoque, categoria, academia_id) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $descricao, float $preco, int $estoque, string $categoria): bool
    {
        $sql = 'UPDATE produto SET nome=?, descricao=?, preco=?, estoque=?, categoria=? WHERE idproduto=? AND academia_id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $id, $this->academyId()]);
    }

    public function findByCategoria(string $categoria): array
    {
        $stmt = $this->db->prepare('SELECT * FROM produto WHERE categoria = ? AND academia_id = ?');
        $stmt->execute([$categoria, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findByNome(string $nome): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produto WHERE nome LIKE ? AND academia_id = ?');
        $stmt->execute(["%{$nome}%", $this->academyId()]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
