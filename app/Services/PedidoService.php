<?php

namespace App\Services;

use App\Repositories\PedidoRepository;

class PedidoService
{
    private PedidoRepository $pedidoRepository;

    public function __construct(PedidoRepository $pedidoRepository)
    {
        $this->pedidoRepository = $pedidoRepository;
    }

    public function createPedido(int $usuarioId, float $valorTotal, string $status = "pendente", string $dataPedido = null): ?int
    {
        if ($dataPedido === null) {
            $dataPedido = date("Y-m-d H:i:s");
        }
        return $this->pedidoRepository->create($usuarioId, $valorTotal, $status, $dataPedido);
    }

    public function updatePedido(int $id, float $valorTotal, string $status): bool
    {
        return $this->pedidoRepository->update($id, $valorTotal, $status);
    }

    public function updatePedidoStatus(int $id, string $status): bool
    {
        return $this->pedidoRepository->updateStatus($id, $status);
    }

    public function getPedidoById(int $id): ?array
    {
        return $this->pedidoRepository->find($id);
    }

    public function getPedidosByUsuarioId(int $usuarioId): array
    {
        return $this->pedidoRepository->findByUsuarioId($usuarioId);
    }

    public function getPedidosByStatus(string $status): array
    {
        return $this->pedidoRepository->findByStatus($status);
    }

    public function deletePedido(int $id): bool
    {
        return $this->pedidoRepository->delete($id);
    }

    public function getAllPedidos(): array
    {
        return $this->pedidoRepository->all();
    }
}
