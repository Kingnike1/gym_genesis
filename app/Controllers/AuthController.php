<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Repositories\UserRepository;
use App\Security\LoginRateLimiter;
use App\Security\SessionManager;
use App\Services\UserService;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService(new UserRepository());
    }

    public function login(): void
    {
        SessionManager::start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('login', ['csrf_token' => SecurityHelper::generateCSRFToken()]);
            return;
        }

        if (!SecurityHelper::verifyCSRFToken((string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            echo 'Token CSRF inválido.';
            return;
        }

        $email = strtolower(SecurityHelper::sanitizeInput((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $client = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $rateLimitKey = $client . '|' . $email;

        if (LoginRateLimiter::tooManyAttempts($rateLimitKey)) {
            http_response_code(429);
            $this->render('login', [
                'errorMessage' => 'Muitas tentativas. Tente novamente em alguns minutos.',
                'csrf_token' => SecurityHelper::generateCSRFToken(),
            ]);
            return;
        }

        if (!SecurityHelper::validateEmail($email)) {
            LoginRateLimiter::hit($rateLimitKey);
            $this->render('login', [
                'errorMessage' => 'E-mail ou senha incorretos.',
                'csrf_token' => SecurityHelper::generateCSRFToken(),
            ]);
            return;
        }

        $user = $this->userService->authenticateUser($email, $password);
        if ($user === null) {
            LoginRateLimiter::hit($rateLimitKey);
            $this->render('login', [
                'errorMessage' => 'E-mail ou senha incorretos.',
                'csrf_token' => SecurityHelper::generateCSRFToken(),
            ]);
            return;
        }

        LoginRateLimiter::clear($rateLimitKey);
        SessionManager::authenticate($user);

        $destinations = [
            1 => '/gym_genesis/admin/dashboard',
            2 => '/gym_genesis/professor/dashboard',
            3 => '/gym_genesis/student/dashboard',
        ];

        header('Location: ' . ($destinations[(int) $user['tipo_usuario']] ?? '/gym_genesis/login'));
        exit();
    }

    public function logout(): void
    {
        SessionManager::start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !SecurityHelper::verifyCSRFToken((string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            echo 'Requisição de logout inválida.';
            return;
        }

        SessionManager::logout();
        header('Location: /gym_genesis/login');
        exit();
    }
}
