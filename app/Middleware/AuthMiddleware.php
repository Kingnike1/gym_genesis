<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Verifica se o usuário está autenticado.
     *
     * @return bool True se o usuário está autenticado, false caso contrário
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]);
    }

    /**
     * Verifica se o usuário tem um tipo específico.
     *
     * @param int $userType O tipo de usuário a ser verificado
     * @return bool True se o usuário tem o tipo especificado, false caso contrário
     */
    public static function hasUserType(int $userType): bool
    {
        return isset($_SESSION["user_type"]) && $_SESSION["user_type"] === $userType;
    }

    /**
     * Redireciona para login se o usuário não está autenticado.
     *
     * @return void
     */
    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header("Location: /gym_genesis/login");
            exit();
        }
    }

    /**
     * Redireciona para login se o usuário não tem o tipo especificado.
     *
     * @param int $userType O tipo de usuário requerido
     * @return void
     */
    public static function requireUserType(int $userType): void
    {
        self::requireAuth();
        if (!self::hasUserType($userType)) {
            http_response_code(403);
            echo '<h1>403 - Acesso Proibido</h1>';
            exit();
        }
    }

    /**
     * Obtém o ID do usuário autenticado.
     *
     * @return int|null O ID do usuário ou null se não autenticado
     */
    public static function getUserId(): ?int
    {
        return $_SESSION["user_id"] ?? null;
    }

    /**
     * Obtém o tipo do usuário autenticado.
     *
     * @return int|null O tipo do usuário ou null se não autenticado
     */
    public static function getUserType(): ?int
    {
        return $_SESSION["user_type"] ?? null;
    }
}
