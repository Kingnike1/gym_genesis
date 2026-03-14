<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Services\FuncionarioService;
use App\Repositories\UserRepository;
use App\Repositories\FuncionarioRepository;

class AdminDashboardController extends Controller
{
    private UserService $userService;
    private FuncionarioService $funcionarioService;

    public function __construct()
    {
        $this->userService = new UserService(new UserRepository());
        $this->funcionarioService = new FuncionarioService(new FuncionarioRepository());
    }

    public function index()
    {
        // Obter dados para a visão geral
        $totalUsers = count($this->userService->getAllUsers());
        $totalProfessors = count($this->funcionarioService->getAllFuncionarios());
        // TODO: Implementar PlanService para obter planos ativos
        $activePlans = 0; // Placeholder

        $this->render("admin/index", [
            "title" => "Dashboard do Administrador",
            "contentView" => __DIR__ . "/../Views/admin/dashboard_content.php",
            "totalUsers" => $totalUsers,
            "totalProfessors" => $totalProfessors,
            "activePlans" => $activePlans,
        ]);
    }
}
