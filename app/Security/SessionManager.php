<?php

namespace App\Security;

final class SessionManager
{
    private const IDLE_TIMEOUT = 1800;

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
            session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax']);
            session_start();
        }
        self::enforceIdleTimeout();
    }

    public static function authenticate(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['idusuario'];
        $_SESSION['user_email'] = (string) $user['email'];
        $_SESSION['user_type'] = (int) $user['tipo_usuario'];
        $_SESSION['session_version'] = (int) ($user['session_version'] ?? 1);
        $_SESSION['last_activity'] = time();
    }

    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) self::start();
        self::destroyActiveSession();
    }

    public static function invalidateCurrent(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) self::destroyActiveSession();
    }

    private static function enforceIdleTimeout(): void
    {
        $lastActivity = (int) ($_SESSION['last_activity'] ?? 0);
        if ($lastActivity > 0 && (time() - $lastActivity) > self::IDLE_TIMEOUT) {
            self::destroyActiveSession();
            return;
        }
        $_SESSION['last_activity'] = time();
    }

    private static function destroyActiveSession(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
    }
}
