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

    public function startExecution(int $id): void
    {
        $aluno = $this->currentStudent();
        $executionId = $this->treinoService->startExecution($id, (int) $aluno['idaluno'], $this->nullable($_POST['observacoes'] ?? null));
        if ($executionId <= 0) {
            http_response_code(403);
            echo '<h1>403 - Treino indisponível</h1>';
            return;
        }

        $this->redirect('/student/treinos/' . $id);
    }

    public function finishExecution(int $id): void
    {
        $aluno = $this->currentStudent();
        $updated = $this->treinoService->finishExecution($id, (int) $aluno['idaluno'], $this->nullable($_POST['observacoes'] ?? null));
        if (!$updated) {
            http_response_code(404);
            echo '<h1>404 - Execução não encontrada</h1>';
            return;
        }

        $this->redirect('/student/treinos');
    }

    private function currentStudent(): array
    {
        $userId = AuthMiddleware::getUserId();
        $aluno = $userId ? $this->alunoService->getAlunoByUsuarioId($userId) : null;
        if (!$aluno || $aluno['status'] !== 'ativo') {
            http_response_code(403);
            echo '<h1>403 - Perfil de aluno ativo obrigatório</h1>';
            exit();
        }
        return $aluno;
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Treino Não Encontrado</h1>';
        exit();
    }
}
