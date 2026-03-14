<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Repositories\AddressRepository;
use App\Services\AddressService;

class UserController extends Controller
{
    private UserService $userService;
    private AddressService $addressService;

    public function __construct()
    {
        $this->userService = new UserService(new UserRepository());
        $this->addressService = new AddressService(new AddressRepository());
    }

    public function index(): void
    {
        // Exibir lista de usuários (apenas para administradores)
        // Implementar verificação de permissão aqui
        $users = $this->userService->getAllUsers();
        $this->render("admin/users/index", ["users" => $users]);
    }

    public function create(): void
    {
        // Exibir formulário de criação de usuário
        $this->render("admin/users/create");
    }

    public function store(): void
    {
        // Processar o cadastro de um novo usuário
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST["email"] ?? "";
            $password = $_POST["password"] ?? "";
            $userType = (int)($_POST["user_type"] ?? 3); // Padrão: Aluno

            $userId = $this->userService->registerUser($password, $email, $userType);

            if ($userId) {
                $this->redirect("/admin/users");
            } else {
                // Lidar com erro de cadastro
                $this->render("admin/users/create", ["errorMessage" => "Erro ao cadastrar usuário."]);
            }
        }
    }

    public function edit(int $id): void
    {
        // Exibir formulário de edição de usuário
        $user = $this->userService->getUserById($id);
        if (!$user) {
            $this->handleNotFound();
        }
        $this->render("admin/users/edit", ["user" => $user]);
    }

    public function update(int $id): void
    {
        // Processar a atualização de um usuário existente
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST["email"] ?? "";
            $password = $_POST["password"] ?? ""; // Pode ser vazio se a senha não for alterada
            $userType = (int)($_POST["user_type"] ?? 3);

            $updated = $this->userService->updateUser($id, $password, $email, $userType);

            if ($updated) {
                $this->redirect("/admin/users");
            } else {
                // Lidar com erro de atualização
                $user = $this->userService->getUserById($id);
                $this->render("admin/users/edit", ["user" => $user, "errorMessage" => "Erro ao atualizar usuário."]);
            }
        }
    }

    public function delete(int $id): void
    {
        // Processar a exclusão de um usuário
        $deleted = $this->userService->deleteUser($id);
        if ($deleted) {
            $this->redirect("/admin/users");
        } else {
            // Lidar com erro de exclusão
            // Talvez redirecionar para a lista de usuários com uma mensagem de erro
            $this->redirect("/admin/users");
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Usuário Não Encontrado</h1>';
        exit();
    }
}
