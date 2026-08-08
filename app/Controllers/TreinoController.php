<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Repositories\ProfessorRepository;
use App\Repositories\TreinoRepository;
use App\Services\ProfessorService;
use App\Services\TreinoService;

class TreinoController extends Controller
{
    private TreinoService $treinoService;
    private ProfessorRepository $professorRepository;

    public function __construct()
    {
        $this->treinoService = new TreinoService(new TreinoRepository());
        $this->professorRepository = new ProfessorRepository();
    }

    public function index(): void
    {
        $professor = $this->currentProfessor();
        $treinos = $this->treinoService->getTreinosByProfessorId((int) $professor['idprofessor']);
        $this->render('professor/treinos/index', ['treinos' => $treinos]);
    }

    public function create(): void
    {
        $professor = $this->currentProfessor();
        $students = (new ProfessorService($this->professorRepository))->students((int) $professor['idprofessor']);
        $this->render('professor/treinos/create', ['alunos' => $students]);
    }

    public function store(): void
    {
        $professor = $this->currentProfessor();
        $alunoId = (int) ($_POST['aluno_id'] ?? 0);

        if (!$this->professorHasStudent((int) $professor['idprofessor'], $alunoId)) {
            http_response_code(403);
            echo '<h1>403 - Aluno não vinculado ao professor</h1>';
            return;
        }

        $treinoId = $this->treinoService->createTreino(
            $alunoId,
            (int) $professor['idprofessor'],
            (string) ($_POST['nome'] ?? ''),
            (string) ($_POST['descricao'] ?? ''),
            $_POST['data_inicio'] ?? null,
            $_POST['data_fim'] ?? null,
            is_array($_POST['exercicios'] ?? null) ? $_POST['exercicios'] : [],
        );

        $treinoId > 0 ? $this->redirect('/professor/treinos') : $this->render('professor/treinos/create', ['errorMessage' => 'Erro ao criar treino.']);
    }

    public function edit(int $id): void
    {
        $treino = $this->ownedWorkout($id);
        $this->render('professor/treinos/edit', ['treino' => $treino]);
    }

    public function update(int $id): void
    {
        $this->ownedWorkout($id);
        $updated = $this->treinoService->updateTreino(
            $id,
            (string) ($_POST['nome'] ?? ''),
            (string) ($_POST['descricao'] ?? ''),
            $_POST['data_inicio'] ?? null,
            $_POST['data_fim'] ?? null,
            (string) ($_POST['status'] ?? 'ativo'),
            is_array($_POST['exercicios'] ?? null) ? $_POST['exercicios'] : [],
        );

        $updated ? $this->redirect('/professor/treinos') : $this->render('professor/treinos/edit', ['treino' => $this->treinoService->getTreinoById($id), 'errorMessage' => 'Erro ao atualizar treino.']);
    }

    public function delete(int $id): void
    {
        $this->ownedWorkout($id);
        $this->treinoService->deleteTreino($id);
        $this->redirect('/professor/treinos');
    }

    private function currentProfessor(): array
    {
        $userId = AuthMiddleware::getUserId();
        $professor = $userId ? $this->professorRepository->findByUsuarioId($userId) : null;
        if (!$professor || $professor['status'] !== 'ativo') {
            http_response_code(403);
            echo '<h1>403 - Perfil profissional ativo obrigatório</h1>';
            exit();
        }
        return $professor;
    }

    private function ownedWorkout(int $id): array
    {
        $professor = $this->currentProfessor();
        $treino = $this->treinoService->getTreinoById($id);
        if (!$treino || (int) $treino['professor_id'] !== (int) $professor['idprofessor']) {
            $this->handleNotFound();
        }
        return $treino;
    }

    private function professorHasStudent(int $professorId, int $studentId): bool
    {
        foreach ($this->professorRepository->students($professorId) as $student) {
            if ((int) $student['idaluno'] === $studentId) {
                return true;
            }
        }
        return false;
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Treino Não Encontrado</h1>';
        exit();
    }
}
