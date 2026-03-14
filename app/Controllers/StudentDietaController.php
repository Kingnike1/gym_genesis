<?php

namespace App\Controllers;

use App\Services\DietaService;
use App\Repositories\DietaRepository;
use App\Middleware\AuthMiddleware;

class StudentDietaController extends Controller
{
    private DietaService $dietaService;

    public function __construct()
    {
        AuthMiddleware::requireUserType(3);
        $this->dietaService = new DietaService(new DietaRepository());
    }

    public function index(): void
    {
        $userId = AuthMiddleware::getUserId();
        $dietas = $this->dietaService->getDietasByAlunoId($userId);
        $this->render("student/dietas/index", ["dietas" => $dietas]);
    }

    public function show(int $id): void
    {
        $dieta = $this->dietaService->getDietaById($id);
        if (!$dieta) {
            $this->handleNotFound();
        }
        $this->render("student/dietas/show", ["dieta" => $dieta]);
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Dieta Não Encontrada</h1>';
        exit();
    }
}
