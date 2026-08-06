<?php

namespace App\Repositories;

class PedidoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('pedido', 'idpedido', true);
    }

    public function create(int $usuarioId, float $valorTotal, string $status, string $dataPedido): ?int
    {
        $sql = 'INSERT INTO pedido (usuario_id, valor_total, status, data_pedido, academia_id) VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $valorTotal, $status, $dataPedido, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, float $valorTotal, string $status): bool
    {
        $sql = 'UPDATE pedido SET valor_total=?, status=? WHERE idpedido=? AND academia_id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$valorTotal, $status, $id, $this->academyId()]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = 'UPDATE pedido SET status=? WHERE idpedido=? AND academia_id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id, $this->academyId()]);
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM pedido WHERE usuario_id = ? AND academia_id = ?');
        $stmt->execute([$usuarioId, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare('SELECT * FROM pedido WHERE status = ? AND academia_id = ?');
        $stmt->execute([$status, $this->academyId()]);
        return $stmt->fetchAll();
    }
}
