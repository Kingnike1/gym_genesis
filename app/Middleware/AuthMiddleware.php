<?php

namespace App\Middleware;

use App\Routes\Router;
use App\Security\SessionManager;

class AuthMiddleware
{
    public static function isAuthenticated(): bool
    {
        SessionManager::start();
        return !empty($_SESSION['user_id']);
    }

    public static function hasUserType(int $userType): bool
    {
        SessionManager::start();
        return isset($_SESSION['user_type']) && (int) $_SESSION['user_type'] === $userType;
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: ' . Router::url('/login'));
            exit();
        }
    }

    public static function requireUserType(int $userType): void
    {
        self::requireAuth();
        if (!self::hasUserType($userType)) {
            http_response_code(403);
            echo '<h1>403 - Acesso Proibido</h1>';
            exit();
        }
    }

    public static function getUserId(): ?int
    {
        SessionManager::start();
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function getUserType(): ?int
    {
        SessionManager::start();
        return isset($_SESSION['user_type']) ? (int) $_SESSION['user_type'] : null;
    }
}
