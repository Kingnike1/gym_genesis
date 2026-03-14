<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class PedidoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct("pedido", "idpedido");
    }

    public function create(int $usuarioId, float $valorTotal, string $status, string $dataPedido): ?int
    {
        $sql = "INSERT INTO pedido (usuario_id, valor_total, status, data_pedido) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $valorTotal, $status, $dataPedido]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, float $valorTotal, string $status): bool
    {
        $sql = "UPDATE pedido SET valor_total=?, status=? WHERE idpedido=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$valorTotal, $status, $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE pedido SET status=? WHERE idpedido=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pedido WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pedido WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
}
