<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Repositories\AlunoRepository;
use App\Repositories\TreinoRepository;
use App\Services\AlunoService;
use App\Services\TreinoService;

class StudentTreinoController extends Controller
{
    private TreinoService $treinoService;
    private AlunoService $alunoService;

    public function __construct()
    {
        AuthMiddleware::requireUserType(3);
        $this->treinoService = new TreinoService(new TreinoRepository());
        $this->alunoService = new AlunoService(new AlunoRepository());
    }

    public function index(): void
    {
        $aluno = $this->currentStudent();
        $treinos = $this->treinoService->getTreinosByAlunoId((int) $aluno['idaluno']);
        $this->render('student/treinos/index', ['treinos' => $treinos]);
    }

    public function show(int $id): void
    {
        $aluno = $this->currentStudent();
        $treino = $this->treinoService->getTreinoById($id);
        if (!$treino || (int) $treino['aluno_id'] !== (int) $aluno['idaluno']) {
            $this->handleNotFound();
        }

        $this->render('student/treinos/show', ['treino' => $treino]);
    }

    private function currentStudent(): array
    {
        $userId = AuthMiddleware::getUserId();
        $aluno = $userId ? $this->alunoService->getAlunoByUsuarioId($userId) : null;
        if (!$aluno) {
            $this->handleNotFound();
        }
        return $aluno;
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Treino Não Encontrado</h1>';
        exit();
    }
}
