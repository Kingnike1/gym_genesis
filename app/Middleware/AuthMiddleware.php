<?php

namespace App\Middleware;

use App\Enums\UserRole;
use App\Routes\Router;
use App\Security\SessionManager;

final class AuthMiddleware
{
    public static function isAuthenticated(): bool
    {
        SessionManager::start();
        return !empty($_SESSION['user_id']);
    }

    public static function hasRole(UserRole $role): bool
    {
        SessionManager::start();
        return isset($_SESSION['user_type']) && (int) $_SESSION['user_type'] === $role->value;
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: ' . Router::url('/login'));
            exit();
        }
    }

    public static function requireRole(UserRole $role): void
    {
        self::requireAuth();
        if (!self::hasRole($role)) {
            http_response_code(403);
            echo '<h1>403 - Acesso Proibido</h1>';
            exit();
        }
    }

    public static function requireUserType(int $userType): void
    {
        self::requireRole(UserRole::fromInput($userType));
    }

    public static function getUserId(): ?int
    {
        SessionManager::start();
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function getUserRole(): ?UserRole
    {
        SessionManager::start();
        return isset($_SESSION['user_type']) ? UserRole::tryFrom((int) $_SESSION['user_type']) : null;
    }

    public static function getUserType(): ?int
    {
        return self::getUserRole()?->value;
    }
}
