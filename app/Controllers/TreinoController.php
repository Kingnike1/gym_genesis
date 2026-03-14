<?php

namespace App\Controllers;

use App\Services\TreinoService;
use App\Repositories\TreinoRepository;

class TreinoController extends Controller
{
    private TreinoService $treinoService;

    public function __construct()
    {
        $this->treinoService = new TreinoService(new TreinoRepository());
    }

    public function index(): void
    {
        $treinos = $this->treinoService->getAllTreinos();
        $this->render("professor/treinos/index", ["treinos" => $treinos]);
    }

    public function create(): void
    {
        $this->render("professor/treinos/create");
    }

    public function store(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $alunoId = (int)($_POST["aluno_id"] ?? 0);
            $professorId = (int)($_POST["professor_id"] ?? 0);
            $nome = $_POST["nome"] ?? "";
            $descricao = $_POST["descricao"] ?? "";

            $treinoId = $this->treinoService->createTreino($alunoId, $professorId, $nome, $descricao);

            if ($treinoId) {
                $this->redirect("/professor/treinos");
            } else {
                $this->render("professor/treinos/create", ["errorMessage" => "Erro ao criar treino."]);
            }
        }
    }

    public function edit(int $id): void
    {
        $treino = $this->treinoService->getTreinoById($id);
        if (!$treino) {
            $this->handleNotFound();
        }
        $this->render("professor/treinos/edit", ["treino" => $treino]);
    }

    public function update(int $id): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nome = $_POST["nome"] ?? "";
            $descricao = $_POST["descricao"] ?? "";

            $updated = $this->treinoService->updateTreino($id, $nome, $descricao);

            if ($updated) {
                $this->redirect("/professor/treinos");
            } else {
                $treino = $this->treinoService->getTreinoById($id);
                $this->render("professor/treinos/edit", ["treino" => $treino, "errorMessage" => "Erro ao atualizar treino."]);
            }
        }
    }

    public function delete(int $id): void
    {
        $deleted = $this->treinoService->deleteTreino($id);
        if ($deleted) {
            $this->redirect("/professor/treinos");
        } else {
            $this->redirect("/professor/treinos");
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Treino Não Encontrado</h1>';
        exit();
    }
}
