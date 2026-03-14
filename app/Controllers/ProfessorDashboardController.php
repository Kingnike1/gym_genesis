<?php

namespace App\Controllers;

use App\Services\AlunoService;
use App\Services\TreinoService;
use App\Services\DietaService;
use App\Repositories\AlunoRepository;
use App\Repositories\TreinoRepository;
use App\Repositories\DietaRepository;

class ProfessorDashboardController extends Controller
{
    private AlunoService $alunoService;
    private TreinoService $treinoService;
    private DietaService $dietaService;

    public function __construct()
    {
        $this->alunoService = new AlunoService(new AlunoRepository());
        $this->treinoService = new TreinoService(new TreinoRepository());
        $this->dietaService = new DietaService(new DietaRepository());
    }

    public function index()
    {
        // Obter dados para a visão geral do professor
        $totalAlunos = count($this->alunoService->getAllAlunos());
        $totalTreinos = count($this->treinoService->getAllTreinos());
        $totalDietas = count($this->dietaService->getAllDietas());

        $this->render("professor/index", [
            "title" => "Dashboard do Professor",
            "contentView" => __DIR__ . "/../Views/professor/dashboard_content.php",
            "totalAlunos" => $totalAlunos,
            "totalTreinos" => $totalTreinos,
            "totalDietas" => $totalDietas,
        ]);
    }
}
