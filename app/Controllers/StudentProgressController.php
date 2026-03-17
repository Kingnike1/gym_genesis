<?php

namespace App\Controllers;

use App\Services\AvaliacaoFisicaService;
use App\Repositories\AvaliacaoFisicaRepository;
use App\Middleware\AuthMiddleware;

class StudentProgressController extends Controller
{
    private AvaliacaoFisicaService $avaliacaoService;

    public function __construct()
    {
        // Verificar se o usuário está autenticado e é um aluno (tipo 3)
        AuthMiddleware::requireUserType(3);

        $this->avaliacaoService = new AvaliacaoFisicaService(new AvaliacaoFisicaRepository());
    }

    public function index()
    {
        $userId = AuthMiddleware::getUserId();
        $progress = $this->avaliacaoService->calculateProgress($userId);
        $latestAvaliacao = $this->avaliacaoService->getLatestAvaliacaoByUsuarioId($userId);

        $this->render("student/index", [
            "title" => "Meu Progresso",
            "contentView" => __DIR__ . "/../Views/student/progress_content.php",
            "progress" => $progress,
            "latestAvaliacao" => $latestAvaliacao,
        ]);
    }

    public function avaliacoes()
    {
        $userId = AuthMiddleware::getUserId();
        $avaliacoes = $this->avaliacaoService->getAvaliacoesByUsuarioId($userId);

        $this->render("student/index", [
            "title" => "Minhas Avaliações",
            "contentView" => __DIR__ . "/../Views/student/avaliacoes_list.php",
            "avaliacoes" => $avaliacoes,
        ]);
    }

    public function create()
    {
        $this->render("student/index", [
            "title" => "Nova Avaliação Física",
            "contentView" => __DIR__ . "/../Views/student/avaliacao_create.php",
        ]);
    }

    public function store()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $userId = AuthMiddleware::getUserId();
            $peso = (float)($_POST["peso"] ?? 0);
            $altura = (float)($_POST["altura"] ?? 0);
            $percentualGordura = !empty($_POST["percentual_gordura"]) ? (float)$_POST["percentual_gordura"] : null;

            if ($peso <= 0 || $altura <= 0) {
                $this->render("student/index", [
                    "title" => "Nova Avaliação Física",
                    "contentView" => __DIR__ . "/../Views/student/avaliacao_create.php",
                    "errorMessage" => "Peso e altura devem ser maiores que zero.",
                ]);
                return;
            }

            $avaliacaoId = $this->avaliacaoService->createAvaliacao($peso, $altura, $percentualGordura, $userId);

            if ($avaliacaoId) {
                $this->redirect("/student/avaliacoes");
            } else {
                $this->render("student/index", [
                    "title" => "Nova Avaliação Física",
                    "contentView" => __DIR__ . "/../Views/student/avaliacao_create.php",
                    "errorMessage" => "Erro ao criar avaliação.",
                ]);
            }
        }
    }

    public function show(int $id)
    {
        $userId = AuthMiddleware::getUserId();
        $avaliacao = $this->avaliacaoService->getAvaliacaoById($id);

        if (!$avaliacao || $avaliacao['usuario_id'] != $userId) {
            $this->handleNotFound();
        }

        $this->render("student/index", [
            "title" => "Detalhes da Avaliação",
            "contentView" => __DIR__ . "/../Views/student/avaliacao_show.php",
            "avaliacao" => $avaliacao,
        ]);
    }

    public function edit(int $id)
    {
        $userId = AuthMiddleware::getUserId();
        $avaliacao = $this->avaliacaoService->getAvaliacaoById($id);

        if (!$avaliacao || $avaliacao['usuario_id'] != $userId) {
            $this->handleNotFound();
        }

        $this->render("student/index", [
            "title" => "Editar Avaliação",
            "contentView" => __DIR__ . "/../Views/student/avaliacao_edit.php",
            "avaliacao" => $avaliacao,
        ]);
    }

    public function update(int $id)
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $userId = AuthMiddleware::getUserId();
            $avaliacao = $this->avaliacaoService->getAvaliacaoById($id);

            if (!$avaliacao || $avaliacao['usuario_id'] != $userId) {
                $this->handleNotFound();
            }

            $peso = (float)($_POST["peso"] ?? 0);
            $altura = (float)($_POST["altura"] ?? 0);
            $percentualGordura = !empty($_POST["percentual_gordura"]) ? (float)$_POST["percentual_gordura"] : null;
            $dataAvaliacao = $_POST["data_avaliacao"] ?? date('Y-m-d');

            if ($peso <= 0 || $altura <= 0) {
                $this->render("student/index", [
                    "title" => "Editar Avaliação",
                    "contentView" => __DIR__ . "/../Views/student/avaliacao_edit.php",
                    "avaliacao" => $avaliacao,
                    "errorMessage" => "Peso e altura devem ser maiores que zero.",
                ]);
                return;
            }

            $updated = $this->avaliacaoService->updateAvaliacao($id, $peso, $altura, $percentualGordura, $dataAvaliacao);

            if ($updated) {
                $this->redirect("/student/avaliacoes");
            } else {
                $avaliacao = $this->avaliacaoService->getAvaliacaoById($id);
                $this->render("student/index", [
                    "title" => "Editar Avaliação",
                    "contentView" => __DIR__ . "/../Views/student/avaliacao_edit.php",
                    "avaliacao" => $avaliacao,
                    "errorMessage" => "Erro ao atualizar avaliação.",
                ]);
            }
        }
    }

    public function delete(int $id)
    {
        $userId = AuthMiddleware::getUserId();
        $avaliacao = $this->avaliacaoService->getAvaliacaoById($id);

        if (!$avaliacao || $avaliacao['usuario_id'] != $userId) {
            $this->handleNotFound();
        }

        $deleted = $this->avaliacaoService->deleteAvaliacao($id);
        if ($deleted) {
            $this->redirect("/student/avaliacoes");
        } else {
            $this->redirect("/student/avaliacoes");
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Avaliação Não Encontrada</h1>';
        exit();
    }
}
