<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Helpers\SecurityHelper;
use App\Middleware\AuthMiddleware;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService(new UserRepository());
    }

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Verificar token CSRF
            $csrfToken = $_POST["csrf_token"] ?? "";
            if (!SecurityHelper::verifyCSRFToken($csrfToken)) {
                http_response_code(403);
                echo "Token CSRF inválido.";
                exit();
            }

            $email = SecurityHelper::sanitizeInput($_POST["email"] ?? "");
            $password = $_POST["password"] ?? "";

            // Validar e-mail
            if (!SecurityHelper::validateEmail($email)) {
                $this->render("login", ["errorMessage" => "E-mail inválido."]);
                return;
            }

            $user = $this->userService->authenticateUser($email, $password);

            if ($user) {
                // Iniciar sessão e redirecionar para o dashboard apropriado
                session_start();
                $_SESSION["user_id"] = $user["idusuario"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_type"] = $user["tipo_usuario"];

                switch ($user["tipo_usuario"]) {
                    case 1: // Administrador
                        header("Location: /gym_genesis/admin/dashboard");
                        break;
                    case 2: // Professor
                        header("Location: /gym_genesis/professor/dashboard");
                        break;
                    case 3: // Aluno
                        header("Location: /gym_genesis/student/dashboard");
                        break;
                    default:
                        header("Location: /gym_genesis/login"); // Tipo de usuário desconhecido
                        break;
                }
                exit();
            } else {
                // Falha na autenticação
                $csrfToken = SecurityHelper::generateCSRFToken();
                $this->render("login", [
                    "errorMessage" => "E-mail ou senha incorretos.",
                    "csrf_token" => $csrfToken
                ]);
            }
        } else {
            // Exibir formulário de login
            session_start();
            $csrfToken = SecurityHelper::generateCSRFToken();
            $this->render("login", ["csrf_token" => $csrfToken]);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /gym_genesis/login");
        exit();
    }
}
