<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Services\DietaService;

class DietaController extends Controller
{
    public function __construct(private readonly DietaService $dietaService)
    {
    }

    public function index(): void
    {
        $userId = AuthMiddleware::getUserId();
        $dietas = $userId ? $this->dietaService->getDietasByResponsibleUserId($userId) : [];
        $this->render('professor/dietas/index', ['dietas' => $dietas]);
    }

    public function create(): void
    {
        $this->render('professor/dietas/create');
    }

    public function store(): void
    {
        $userId = AuthMiddleware::getUserId();
        if (!$userId) {
            http_response_code(401);
            return;
        }

        try {
            $this->dietaService->createDieta($_POST, $userId);
            $this->redirect('/professor/dietas');
        } catch (\InvalidArgumentException $exception) {
            $this->render('professor/dietas/create', ['errorMessage' => $exception->getMessage()]);
        }
    }

    public function edit(int $id): void
    {
        $dieta = $this->dietaService->getDietaById($id);
        if (!$dieta || (int) $dieta['responsavel_usuario_id'] !== (int) AuthMiddleware::getUserId()) {
            $this->handleNotFound();
        }
        $this->render('professor/dietas/edit', ['dieta' => $dieta]);
    }

    public function update(int $id): void
    {
        $userId = AuthMiddleware::getUserId();
        $dieta = $this->dietaService->getDietaById($id);
        if (!$userId || !$dieta || (int) $dieta['responsavel_usuario_id'] !== $userId) {
            http_response_code(403);
            return;
        }

        try {
            $this->dietaService->updateDieta($id, $_POST, $userId);
            $this->redirect('/professor/dietas');
        } catch (\InvalidArgumentException $exception) {
            $this->render('professor/dietas/edit', ['dieta' => $dieta, 'errorMessage' => $exception->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        $dieta = $this->dietaService->getDietaById($id);
        if (!$dieta || (int) $dieta['responsavel_usuario_id'] !== (int) AuthMiddleware::getUserId()) {
            http_response_code(403);
            return;
        }
        $this->dietaService->deleteDieta($id);
        $this->redirect('/professor/dietas');
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Plano Alimentar Não Encontrado</h1>';
        exit();
    }
}
