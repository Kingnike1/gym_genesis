<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Repositories\AlunoRepository;
use App\Repositories\DietaRepository;
use App\Services\DietaService;

class StudentDietaController extends Controller
{
    private DietaService $dietaService;
    private AlunoRepository $alunoRepository;

    public function __construct()
    {
        AuthMiddleware::requireUserType(3);
        $this->dietaService = new DietaService(new DietaRepository());
        $this->alunoRepository = new AlunoRepository();
    }

    public function index(): void
    {
        $aluno = $this->currentStudent();
        $dietas = $aluno ? $this->dietaService->getDietasByAlunoId((int) $aluno['idaluno']) : [];
        $this->render('student/dietas/index', ['dietas' => $dietas]);
    }

    public function show(int $id): void
    {
        $aluno = $this->currentStudent();
        $dieta = $this->dietaService->getDietaById($id);
        if (!$aluno || !$dieta || (int) $dieta['aluno_id'] !== (int) $aluno['idaluno']) {
            $this->handleNotFound();
        }

        $this->render('student/dietas/show', ['dieta' => $dieta]);
    }

    private function currentStudent(): ?array
    {
        $userId = AuthMiddleware::getUserId();
        return $userId ? $this->alunoRepository->findByUsuarioId($userId) : null;
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Plano Alimentar Não Encontrado</h1>';
        exit();
    }
}
