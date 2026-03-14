<?php

namespace App\Controllers;

use App\Services\TreinoService;
use App\Repositories\TreinoRepository;
use App\Middleware\AuthMiddleware;

class StudentTreinoController extends Controller
{
    private TreinoService $treinoService;

    public function __construct()
    {
        AuthMiddleware::requireUserType(3);
        $this->treinoService = new TreinoService(new TreinoRepository());
    }

    public function index(): void
    {
        $userId = AuthMiddleware::getUserId();
        $treinos = $this->treinoService->getTreinosByAlunoId($userId);
        $this->render("student/treinos/index", ["treinos" => $treinos]);
    }

    public function show(int $id): void
    {
        $treino = $this->treinoService->getTreinoById($id);
        if (!$treino) {
            $this->handleNotFound();
        }
        $this->render("student/treinos/show", ["treino" => $treino]);
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Treino Não Encontrado</h1>';
        exit();
    }
}
