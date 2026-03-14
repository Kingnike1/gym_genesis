<?php

namespace App\Controllers;

use App\Services\DietaService;
use App\Repositories\DietaRepository;

class DietaController extends Controller
{
    private DietaService $dietaService;

    public function __construct()
    {
        $this->dietaService = new DietaService(new DietaRepository());
    }

    public function index(): void
    {
        $dietas = $this->dietaService->getAllDietas();
        $this->render("professor/dietas/index", ["dietas" => $dietas]);
    }

    public function create(): void
    {
        $this->render("professor/dietas/create");
    }

    public function store(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $alunoId = (int)($_POST["aluno_id"] ?? 0);
            $professorId = (int)($_POST["professor_id"] ?? 0);
            $nome = $_POST["nome"] ?? "";
            $descricao = $_POST["descricao"] ?? "";

            $dietaId = $this->dietaService->createDieta($alunoId, $professorId, $nome, $descricao);

            if ($dietaId) {
                $this->redirect("/professor/dietas");
            } else {
                $this->render("professor/dietas/create", ["errorMessage" => "Erro ao criar dieta."]);
            }
        }
    }

    public function edit(int $id): void
    {
        $dieta = $this->dietaService->getDietaById($id);
        if (!$dieta) {
            $this->handleNotFound();
        }
        $this->render("professor/dietas/edit", ["dieta" => $dieta]);
    }

    public function update(int $id): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nome = $_POST["nome"] ?? "";
            $descricao = $_POST["descricao"] ?? "";

            $updated = $this->dietaService->updateDieta($id, $nome, $descricao);

            if ($updated) {
                $this->redirect("/professor/dietas");
            } else {
                $dieta = $this->dietaService->getDietaById($id);
                $this->render("professor/dietas/edit", ["dieta" => $dieta, "errorMessage" => "Erro ao atualizar dieta."]);
            }
        }
    }

    public function delete(int $id): void
    {
        $deleted = $this->dietaService->deleteDieta($id);
        if ($deleted) {
            $this->redirect("/professor/dietas");
        } else {
            $this->redirect("/professor/dietas");
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Dieta Não Encontrada</h1>';
        exit();
    }
}
