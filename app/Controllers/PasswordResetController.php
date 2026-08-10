<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Routes\Router;
use App\Security\LoginRateLimiter;
use App\Services\PasswordResetMailer;
use App\Services\PasswordResetService;

final class PasswordResetController extends Controller
{
    public function __construct(private PasswordResetService $resets, private PasswordResetMailer $mailer)
    {
    }

    public function request(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('password/forgot', ['csrf_token' => SecurityHelper::generateCSRFToken()]);
            return;
        }
        if (!SecurityHelper::verifyCSRFToken((string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            echo 'Token CSRF inválido.';
            return;
        }
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $key = 'password-reset|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '|' . $email;
        if (!LoginRateLimiter::tooManyAttempts($key)) {
            LoginRateLimiter::hit($key);
            $token = filter_var($email, FILTER_VALIDATE_EMAIL) ? $this->resets->issueToken($email) : null;
            if ($token !== null) {
                $base = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');
                $url = $base . Router::url('/password/reset') . '?token=' . rawurlencode($token);
                try {
                    $this->mailer->send($email, $url);
                } catch (\Throwable $e) {
                    error_log('Password reset mail failed: ' . $e->getMessage());
                }
            }
        }
        $this->render('password/forgot', [
            'csrf_token' => SecurityHelper::generateCSRFToken(),
            'successMessage' => 'Se existir uma conta com esse e-mail, enviaremos instruções para redefinir a senha.',
        ]);
    }

    public function reset(): void
    {
        $token = (string) ($_POST['token'] ?? $_GET['token'] ?? '');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('password/reset', ['token' => $token, 'csrf_token' => SecurityHelper::generateCSRFToken()]);
            return;
        }
        if (!SecurityHelper::verifyCSRFToken((string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            echo 'Token CSRF inválido.';
            return;
        }
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');
        if ($password !== $confirmation || !$this->resets->reset($token, $password)) {
            $this->render('password/reset', ['token' => $token, 'csrf_token' => SecurityHelper::generateCSRFToken(), 'errorMessage' => 'Link inválido/expirado ou senha inválida.']);
            return;
        }
        header('Location: ' . Router::url('/login'));
        exit();
    }
}
