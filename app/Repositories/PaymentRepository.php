<?php

namespace App\Repositories;

class PaymentRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('pagamento_comercial', 'idpagamento', true);
    }

    public function create(int $pedidoId, string $gateway, string $metodo, string $idempotencyKey, float $valor): int
    {
        $existing = $this->db->prepare(
            'SELECT idpagamento FROM pagamento_comercial WHERE academia_id=? AND idempotency_key=? LIMIT 1'
        );
        $existing->execute([$this->academyId(), $idempotencyKey]);
        if ($row = $existing->fetch()) {
            return (int) $row['idpagamento'];
        }

        $pedido = $this->db->prepare(
            'SELECT valor_total FROM pedido_comercial WHERE idpedido=? AND academia_id=? LIMIT 1'
        );
        $pedido->execute([$pedidoId, $this->academyId()]);
        $order = $pedido->fetch();
        if (!$order || abs((float) $order['valor_total'] - $valor) > 0.009) {
            throw new \DomainException('Valor de pagamento divergente do pedido.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO pagamento_comercial '
            . '(academia_id, pedido_id, gateway, idempotency_key, metodo, valor) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$this->academyId(), $pedidoId, $gateway, $idempotencyKey, $metodo, $valor]);

        return (int) $this->db->lastInsertId();
    }

    public function applyGatewayResult(string $gateway, string $externalId, string $status): bool
    {
        $allowed = ['pendente', 'processando', 'aprovado', 'recusado', 'cancelado', 'reembolsado'];
        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException('Status de pagamento inválido.');
        }

        $stmt = $this->db->prepare(
            'UPDATE pagamento_comercial SET external_id=?, status=? WHERE gateway=? AND academia_id=?'
        );

        return $stmt->execute([$externalId, $status, $gateway, $this->academyId()]);
    }
}
