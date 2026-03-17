<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Middleware\AuthMiddleware;

class StudentProfileController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        // Verificar se o usuário está autenticado e é um aluno (tipo 3)
        AuthMiddleware::requireUserType(3);

        $this->userService = new UserService(new UserRepository());
    }

    public function show()
    {
        $userId = AuthMiddleware::getUserId();
        $user = $this->userService->getUserById($userId);

        if (!$user) {
            $this->handleNotFound();
        }

        $this->render("student/index", [
            "title" => "Meu Perfil",
            "contentView" => __DIR__ . "/../Views/student/profile_content.php",
            "user" => $user,
        ]);
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $userId = AuthMiddleware::getUserId();
            $user = $this->userService->getUserById($userId);

            if (!$user) {
                $this->handleNotFound();
            }

            $email = $_POST["email"] ?? "";
            $senhaAtual = $_POST["senha_atual"] ?? "";
            $novaSenha = $_POST["nova_senha"] ?? "";
            $confirmarSenha = $_POST["confirmar_senha"] ?? "";

            // Validar email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->render("student/index", [
                    "title" => "Meu Perfil",
                    "contentView" => __DIR__ . "/../Views/student/profile_content.php",
                    "user" => $user,
                    "errorMessage" => "Email inválido.",
                ]);
                return;
            }

            // Se tentar mudar senha, validar
            if (!empty($novaSenha)) {
                if ($novaSenha !== $confirmarSenha) {
                    $this->render("student/index", [
                        "title" => "Meu Perfil",
                        "contentView" => __DIR__ . "/../Views/student/profile_content.php",
                        "user" => $user,
                        "errorMessage" => "As senhas não conferem.",
                    ]);
                    return;
                }

                // Verificar se a senha atual está correta
                if (!password_verify($senhaAtual, $user['senha'])) {
                    $this->render("student/index", [
                        "title" => "Meu Perfil",
                        "contentView" => __DIR__ . "/../Views/student/profile_content.php",
                        "user" => $user,
                        "errorMessage" => "Senha atual incorreta.",
                    ]);
                    return;
                }

                $senhaHash = password_hash($novaSenha, PASSWORD_BCRYPT);
                $updated = $this->userService->updateUserPassword($userId, $senhaHash);
            } else {
                // Apenas atualizar email
                $updated = $this->userService->updateUserEmail($userId, $email);
            }

            if ($updated) {
                $this->render("student/index", [
                    "title" => "Meu Perfil",
                    "contentView" => __DIR__ . "/../Views/student/profile_content.php",
                    "user" => $this->userService->getUserById($userId),
                    "successMessage" => "Perfil atualizado com sucesso.",
                ]);
            } else {
                $this->render("student/index", [
                    "title" => "Meu Perfil",
                    "contentView" => __DIR__ . "/../Views/student/profile_content.php",
                    "user" => $user,
                    "errorMessage" => "Erro ao atualizar perfil.",
                ]);
            }
        }
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Usuário Não Encontrado</h1>';
        exit();
    }
}
