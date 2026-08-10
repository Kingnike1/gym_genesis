<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Repositories\AlunoRepository;
use App\Services\AvaliacaoFisicaService;

class StudentProgressController extends Controller
{
    public function __construct(
        private AvaliacaoFisicaService $avaliacaoService,
        private AlunoRepository $alunoRepository
    ) {
        AuthMiddleware::requireUserType(3);
    }

    private function aluno(): array
    {
        $aluno = $this->alunoRepository->findByUsuarioId(AuthMiddleware::getUserId());
        if (!$aluno) {
            $this->handleNotFound();
        }
        return $aluno;
    }

    public function index(): void
    {
        $aluno = $this->aluno();
        $this->render('student/index', [
            'title' => 'Meu Progresso',
            'contentView' => __DIR__ . '/../Views/student/progress_content.php',
            'progress' => $this->avaliacaoService->calculateProgress((int) $aluno['idaluno']),
            'latestAvaliacao' => $this->avaliacaoService->getLatestAvaliacaoByAlunoId((int) $aluno['idaluno']),
        ]);
    }

    public function avaliacoes(): void
    {
        $aluno = $this->aluno();
        $this->render('student/index', [
            'title' => 'Minhas Avaliações',
            'contentView' => __DIR__ . '/../Views/student/avaliacoes_list.php',
            'avaliacoes' => $this->avaliacaoService->getAvaliacoesByAlunoId((int) $aluno['idaluno']),
        ]);
    }

    public function create(): void
    {
        $this->render('student/index', ['title' => 'Nova Avaliação Física', 'contentView' => __DIR__ . '/../Views/student/avaliacao_create.php']);
    }

    public function store(): void
    {
        $aluno = $this->aluno();
        $this->avaliacaoService->createAvaliacao(
            (int) $aluno['idaluno'],
            AuthMiddleware::getUserId(),
            (float) ($_POST['peso'] ?? 0),
            (float) ($_POST['altura'] ?? 0),
            isset($_POST['percentual_gordura']) && $_POST['percentual_gordura'] !== '' ? (float) $_POST['percentual_gordura'] : null,
            trim((string) ($_POST['pressao_arterial'] ?? '')) ?: null,
            trim((string) ($_POST['observacoes'] ?? '')) ?: null
        );
        $this->redirect('/student/avaliacoes');
    }

    public function show(int $id): void
    {
        $aluno = $this->aluno();
        $avaliacao = $this->avaliacaoService->getAvaliacaoById($id);
        if (!$avaliacao || (int) $avaliacao['aluno_id'] !== (int) $aluno['idaluno']) {
            $this->handleNotFound();
        }
        $this->render('student/index', [
            'title' => 'Detalhes da Avaliação',
            'contentView' => __DIR__ . '/../Views/student/avaliacao_show.php',
            'avaliacao' => $avaliacao,
            'medidas' => $this->avaliacaoService->measurements($id),
        ]);
    }

    public function edit(int $id): void { $this->immutable(); }
    public function update(int $id): void { $this->immutable(); }
    public function delete(int $id): void { $this->immutable(); }

    private function immutable(): void
    {
        http_response_code(405);
        header('Allow: GET, POST');
        echo '<h1>405 - Avaliações físicas são registros históricos imutáveis.</h1>';
        exit();
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Avaliação Não Encontrada</h1>';
        exit();
    }
}
