<?php

namespace App\Repositories;

use App\Middleware\AuthMiddleware;
use App\Services\Database;

class ProdutoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('produto', 'idproduto', true);
    }

    public function create(string $nome, string $descricao, float $preco, int $estoque, string $categoria): ?int
    {
        if ($preco < 0 || $estoque < 0) {
            throw new \InvalidArgumentException('Preço e estoque não podem ser negativos.');
        }
        $stmt = $this->db->prepare('INSERT INTO produto (nome, descricao, preco, estoque, categoria, academia_id) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $descricao, float $preco, int $estoque, string $categoria): bool
    {
        $produto = $this->find($id);
        if (!$produto) {
            return false;
        }
        return Database::transaction(function () use ($id, $nome, $descricao, $preco, $estoque, $categoria, $produto): bool {
            if ($estoque < 0) {
                throw new \DomainException('Estoque não pode ficar negativo.');
            }
            $stmt = $this->db->prepare('UPDATE produto SET nome=?, descricao=?, preco=?, estoque=?, categoria=? WHERE idproduto=? AND academia_id=?');
            $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria, $id, $this->academyId()]);
            $delta = $estoque - (int) $produto['estoque'];
            if ($delta !== 0) {
                $this->recordMovement($id, 'ajuste', $delta, (int) $produto['estoque'], $estoque, 'Ajuste administrativo');
            }
            return true;
        });
    }

    public function moveStock(int $id, string $tipo, int $quantidade, ?string $motivo = null): int
    {
        if (!in_array($tipo, ['entrada','saida','ajuste'], true) || $quantidade === 0) {
            throw new \InvalidArgumentException('Movimentação de estoque inválida.');
        }
        return Database::transaction(function () use ($id, $tipo, $quantidade, $motivo): int {
            $stmt = $this->db->prepare('SELECT estoque FROM produto WHERE idproduto=? AND academia_id=? FOR UPDATE');
            $stmt->execute([$id, $this->academyId()]);
            $row = $stmt->fetch();
            if (!$row) {
                throw new \DomainException('Produto não encontrado.');
            }
            $anterior = (int) $row['estoque'];
            $delta = $tipo === 'saida' ? -abs($quantidade) : ($tipo === 'entrada' ? abs($quantidade) : $quantidade);
            $posterior = $anterior + $delta;
            if ($posterior < 0) {
                throw new \DomainException('Estoque insuficiente.');
            }
            $update = $this->db->prepare('UPDATE produto SET estoque=? WHERE idproduto=? AND academia_id=?');
            $update->execute([$posterior, $id, $this->academyId()]);
            $this->recordMovement($id, $tipo, $delta, $anterior, $posterior, $motivo);
            return $posterior;
        });
    }

    private function recordMovement(int $id, string $tipo, int $quantidade, int $anterior, int $posterior, ?string $motivo): void
    {
        $stmt = $this->db->prepare('INSERT INTO estoque_movimentacao (academia_id, produto_id, tipo, quantidade, saldo_anterior, saldo_posterior, motivo, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$this->academyId(), $id, $tipo, $quantidade, $anterior, $posterior, $motivo, AuthMiddleware::getUserId() ?: null]);
    }

    public function movements(int $id): array
    {
        $stmt = $this->db->prepare('SELECT * FROM estoque_movimentacao WHERE produto_id=? AND academia_id=? ORDER BY created_at DESC, idmovimentacao DESC');
        $stmt->execute([$id, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findByCategoria(string $categoria): array
    {
        $stmt = $this->db->prepare('SELECT * FROM produto WHERE categoria=? AND academia_id=?');
        $stmt->execute([$categoria, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findByNome(string $nome): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produto WHERE nome LIKE ? AND academia_id=? LIMIT 1');
        $stmt->execute(["%{$nome}%", $this->academyId()]);
        return $stmt->fetch() ?: null;
    }
}
