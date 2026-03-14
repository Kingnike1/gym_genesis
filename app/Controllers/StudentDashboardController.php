<?php

namespace App\Controllers;

use App\Services\TreinoService;
use App\Services\DietaService;
use App\Repositories\TreinoRepository;
use App\Repositories\DietaRepository;
use App\Middleware\AuthMiddleware;

class StudentDashboardController extends Controller
{
    private TreinoService $treinoService;
    private DietaService $dietaService;

    public function __construct()
    {
        // Verificar se o usuário está autenticado e é um aluno (tipo 3)
        AuthMiddleware::requireUserType(3);

        $this->treinoService = new TreinoService(new TreinoRepository());
        $this->dietaService = new DietaService(new DietaRepository());
    }

    public function index()
    {
        $userId = AuthMiddleware::getUserId();

        // Obter dados para a visão geral do aluno
        $meusTreinos = $this->treinoService->getTreinosByAlunoId($userId);
        $minhasDietas = $this->dietaService->getDietasByAlunoId($userId);

        $this->render("student/index", [
            "title" => "Dashboard do Aluno",
            "contentView" => __DIR__ . "/../Views/student/dashboard_content.php",
            "meusTreinos" => $meusTreinos,
            "minhasDietas" => $minhasDietas,
        ]);
    }
}
