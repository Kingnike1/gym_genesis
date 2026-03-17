<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Services\FuncionarioService;
use App\Services\PlanoService;
use App\Repositories\UserRepository;
use App\Repositories\FuncionarioRepository;
use App\Repositories\PlanoRepository;

class AdminDashboardController extends Controller
{
    private UserService $userService;
    private FuncionarioService $funcionarioService;
    private PlanoService $planoService;

    public function __construct()
    {
        $this->userService = new UserService(new UserRepository());
        $this->funcionarioService = new FuncionarioService(new FuncionarioRepository());
        $this->planoService = new PlanoService(new PlanoRepository());
    }

    public function index()
    {
        // Obter dados para a visão geral
        $totalUsers = count($this->userService->getAllUsers());
        $totalProfessors = count($this->funcionarioService->getAllFuncionarios());
        $activePlans = count($this->planoService->getAllPlanos());

        $this->render("admin/index", [
            "title" => "Dashboard do Administrador",
            "contentView" => __DIR__ . "/../Views/admin/dashboard_content.php",
            "totalUsers" => $totalUsers,
            "totalProfessors" => $totalProfessors,
            "activePlans" => $activePlans,
        ]);
    }
}
