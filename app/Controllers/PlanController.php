<?php

namespace App\Controllers;

use App\Services\PlanoService;
use App\Repositories\PlanoRepository;

class PlanController extends Controller
{
    private PlanoService $planoService;

    public function __construct()
    {
        $this->planoService = new PlanoService(new PlanoRepository());
    }

    public function index(): void
    {
        $planos = $this->planoService->getAllPlanos();
        $this->render("admin/plans/index", ["planos" => $planos]);
    }

    public function create(): void
    {
        $this->render("admin/plans/create");
    }

    public function store(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $tipo = $_POST["tipo"] ?? "";
            $preco = (float)($_POST["preco"] ?? 0);
            $descricao = $_POST["descricao"] ?? "";
            $duriasDias = (int)($_POST["duriasDias"] ?? 30);

            $planoId = $this->planoService->createPlano($tipo, $preco, $descricao, $duriasDias);

            if ($planoId) {
                $this->redirect("/admin/plans");
            } else {
                $this->render("admin/plans/create", ["errorMessage" => "Erro ao criar plano."]);
            }
        }
    }

    public function edit(int $id): void
    {
        $plano = $this->planoService->getPlanoById($id);
        if (!$plano) {
            $this->handleNotFound();
        }
        $this->render("admin/plans/edit", ["plano" => $plano]);
    }

    public function update(int $id): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $tipo = $_POST["tipo"] ?? "";
            $preco = (float)($_POST["preco"] ?? 0);
            $descricao = $_POST["descricao"] ?? "";
            $duriasDias = (int)($_POST["duriasDias"] ?? 30);

            $updated = $this->planoService->updatePlano($id, $tipo, $preco, $descricao, $duriasDias);

            if ($updated) {
                $this->redirect("/admin/plans");
            } else {
                $plano = $this->planoService->getPlanoById($id);
                $this->render("admin/plans/edit", ["plano" => $plano, "errorMessage" => "Erro ao atualizar plano."]);
            }
        }
    }

    public function delete(int $id): void
    {
        $deleted = $this->planoService->deletePlano($id);
        if ($deleted) {
            $this->redirect("/admin/plans");
        } else {
            $this->redirect("/admin/plans");
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Plano Não Encontrado</h1>';
        exit();
    }
}
