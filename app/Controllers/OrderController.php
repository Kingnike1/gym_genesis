<?php

namespace App\Controllers;

use App\Services\PedidoService;
use App\Repositories\PedidoRepository;

class OrderController extends Controller
{
    private PedidoService $pedidoService;

    public function __construct()
    {
        $this->pedidoService = new PedidoService(new PedidoRepository());
    }

    public function index(): void
    {
        $pedidos = $this->pedidoService->getAllPedidos();
        $this->render("admin/orders/index", ["pedidos" => $pedidos]);
    }

    public function show(int $id): void
    {
        $pedido = $this->pedidoService->getPedidoById($id);
        if (!$pedido) {
            $this->handleNotFound();
        }
        $this->render("admin/orders/show", ["pedido" => $pedido]);
    }

    public function updateStatus(int $id): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $status = $_POST["status"] ?? "pendente";

            $updated = $this->pedidoService->updatePedidoStatus($id, $status);

            if ($updated) {
                $this->redirect("/admin/orders/{$id}");
            } else {
                $pedido = $this->pedidoService->getPedidoById($id);
                $this->render("admin/orders/show", ["pedido" => $pedido, "errorMessage" => "Erro ao atualizar status do pedido."]);
            }
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Pedido Não Encontrado</h1>';
        exit();
    }
}
