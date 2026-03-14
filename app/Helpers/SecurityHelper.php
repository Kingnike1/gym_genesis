<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * Gera um token CSRF e o armazena na sessão.
     *
     * @return string O token CSRF gerado
     */
    public static function generateCSRFToken(): string
    {
        if (!isset($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf_token"];
    }

    /**
     * Verifica se o token CSRF fornecido é válido.
     *
     * @param string $token O token CSRF a ser verificado
     * @return bool True se o token é válido, false caso contrário
     */
    public static function verifyCSRFToken(string $token): bool
    {
        if (!isset($_SESSION["csrf_token"])) {
            return false;
        }
        return hash_equals($_SESSION["csrf_token"], $token);
    }

    /**
     * Escapa uma string para evitar ataques XSS.
     *
     * @param string $string A string a ser escapada
     * @return string A string escapada
     */
    public static function escapeHTML(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
    }

    /**
     * Sanitiza uma entrada de usuário removendo tags HTML e scripts.
     *
     * @param string $input A entrada a ser sanitizada
     * @return string A entrada sanitizada
     */
    public static function sanitizeInput(string $input): string
    {
        return strip_tags(trim($input));
    }

    /**
     * Valida um endereço de e-mail.
     *
     * @param string $email O e-mail a ser validado
     * @return bool True se o e-mail é válido, false caso contrário
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida uma URL.
     *
     * @param string $url A URL a ser validada
     * @return bool True se a URL é válida, false caso contrário
     */
    public static function validateURL(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Cria um hash de senha seguro usando bcrypt.
     *
     * @param string $password A senha a ser hasheada
     * @return string O hash da senha
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
    }

    /**
     * Verifica se uma senha corresponde a um hash.
     *
     * @param string $password A senha em texto plano
     * @param string $hash O hash da senha
     * @return bool True se a senha corresponde ao hash, false caso contrário
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
