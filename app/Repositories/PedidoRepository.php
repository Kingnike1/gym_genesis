<?php

namespace App\Repositories;

use App\Services\Database;

class PedidoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('pedido_comercial', 'idpedido', true);
    }

    public function createFromItems(int $usuarioId, string $idempotencyKey, array $items, float $desconto = 0.0, float $frete = 0.0): int
    {
        if ($idempotencyKey === '' || $items === []) {
            throw new \InvalidArgumentException('Pedido sem itens ou chave de idempotência.');
        }

        return Database::transaction(function () use ($usuarioId, $idempotencyKey, $items, $desconto, $frete): int {
            $existing = $this->db->prepare('SELECT idpedido FROM pedido_comercial WHERE academia_id=? AND idempotency_key=? LIMIT 1');
            $existing->execute([$this->academyId(), $idempotencyKey]);
            if ($row = $existing->fetch()) {
                return (int) $row['idpedido'];
            }

            $subtotal = 0.0;
            $resolved = [];
            $productStmt = $this->db->prepare('SELECT idproduto, nome, preco, estoque FROM produto WHERE idproduto=? AND academia_id=? AND status="ativo" FOR UPDATE');
            foreach ($items as $item) {
                $produtoId = (int) ($item['produto_id'] ?? 0);
                $quantidade = (int) ($item['quantidade'] ?? 0);
                if ($produtoId <= 0 || $quantidade <= 0) {
                    throw new \InvalidArgumentException('Item de pedido inválido.');
                }
                $productStmt->execute([$produtoId, $this->academyId()]);
                $produto = $productStmt->fetch();
                if (!$produto || (int) $produto['estoque'] < $quantidade) {
                    throw new \DomainException('Produto indisponível ou estoque insuficiente.');
                }
                $line = round((float) $produto['preco'] * $quantidade, 2);
                $subtotal += $line;
                $resolved[] = [$produto, $quantidade, $line];
            }

            $desconto = max(0, min($desconto, $subtotal));
            $frete = max(0, $frete);
            $total = round($subtotal - $desconto + $frete, 2);
            $stmt = $this->db->prepare('INSERT INTO pedido_comercial (academia_id, usuario_id, idempotency_key, status, subtotal, desconto, frete, valor_total) VALUES (?, ?, ?, "aguardando_pagamento", ?, ?, ?, ?)');
            $stmt->execute([$this->academyId(), $usuarioId, $idempotencyKey, $subtotal, $desconto, $frete, $total]);
            $pedidoId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare('INSERT INTO pedido_item_registro (pedido_id, produto_id, nome_produto, preco_unitario, quantidade, subtotal) VALUES (?, ?, ?, ?, ?, ?)');
            $stockStmt = $this->db->prepare('UPDATE produto SET estoque=estoque-? WHERE idproduto=? AND academia_id=? AND estoque>=?');
            $movementStmt = $this->db->prepare('INSERT INTO estoque_movimentacao (academia_id, produto_id, tipo, quantidade, saldo_anterior, saldo_posterior, motivo, usuario_id) VALUES (?, ?, "saida", ?, ?, ?, ?, ?)');

            foreach ($resolved as [$produto, $quantidade, $line]) {
                $itemStmt->execute([$pedidoId, $produto['idproduto'], $produto['nome'], $produto['preco'], $quantidade, $line]);
                $anterior = (int) $produto['estoque'];
                $posterior = $anterior - $quantidade;
                $stockStmt->execute([$quantidade, $produto['idproduto'], $this->academyId(), $quantidade]);
                if ($stockStmt->rowCount() !== 1) {
                    throw new \DomainException('Falha de concorrência ao reservar estoque.');
                }
                $movementStmt->execute([$this->academyId(), $produto['idproduto'], -$quantidade, $anterior, $posterior, 'Pedido #' . $pedidoId, $usuarioId]);
            }

            return $pedidoId;
        });
    }

    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['pendente','aguardando_pagamento','pago','cancelado','reembolsado'];
        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException('Status de pedido inválido.');
        }
        $stmt = $this->db->prepare('UPDATE pedido_comercial SET status=? WHERE idpedido=? AND academia_id=?');
        return $stmt->execute([$status, $id, $this->academyId()]);
    }

    public function items(int $id): array
    {
        $stmt = $this->db->prepare('SELECT i.* FROM pedido_item_registro i INNER JOIN pedido_comercial p ON p.idpedido=i.pedido_id WHERE i.pedido_id=? AND p.academia_id=?');
        $stmt->execute([$id, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM pedido_comercial WHERE usuario_id=? AND academia_id=? ORDER BY created_at DESC');
        $stmt->execute([$usuarioId, $this->academyId()]);
        return $stmt->fetchAll();
    }
}
