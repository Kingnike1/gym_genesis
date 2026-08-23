<?php

namespace App\Middleware;

final class RouteAuthorizationMiddleware
{
    private const PREFIX_ROLES = [
        '/admin' => 1,
        '/professor' => 2,
        '/student' => 3,
    ];

    public static function handle(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        foreach (self::PREFIX_ROLES as $prefix => $role) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                AuthMiddleware::requireUserType($role);
                return;
            }
        }
    }

    public static function deny(): never
    {
        http_response_code(403);
        echo '<h1>403 - Acesso Proibido</h1>';
        exit();
    }

    public static function requireOwner(int $ownerId): void
    {
        if (AuthMiddleware::getUserId() !== $ownerId) {
            self::deny();
        }
    }
}
