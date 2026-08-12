<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Security\SessionManager;
use App\Services\Database;
use App\Tenancy\AcademyContext;
use PDO;

final class ApiTokenMiddleware
{
    private static array $scopes = [];
    private static ?int $tokenId = null;

    public static function authenticate(): void
    {
        $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!preg_match('/^Bearer\s+([A-Fa-f0-9]{64})$/', trim($header), $matches)) {
            throw new AuthenticationException('Token Bearer ausente ou inválido.');
        }

        $hash = hash('sha256', $matches[1]);
        $sql = "SELECT t.idtoken, t.usuario_id, t.academia_id, t.scopes, au.unidade_id, au.papel
                FROM api_token t
                INNER JOIN academia_usuario au ON au.usuario_id = t.usuario_id AND au.academia_id = t.academia_id
                INNER JOIN usuario u ON u.idusuario = t.usuario_id
                INNER JOIN academias a ON a.idacademia = t.academia_id
                WHERE t.token_hash = ? AND t.revogado_em IS NULL
                  AND (t.expira_em IS NULL OR t.expira_em > CURRENT_TIMESTAMP)
                  AND au.ativo = 1 AND u.status = 'ativo' AND a.status = 'ativa'
                LIMIT 1";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([$hash]);
        $token = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$token) {
            throw new AuthenticationException('Token expirado, revogado ou inválido.');
        }

        self::$tokenId = (int) $token['idtoken'];
        self::$scopes = json_decode((string) $token['scopes'], true, 512, JSON_THROW_ON_ERROR);
        SessionManager::start();
        $_SESSION['user_id'] = (int) $token['usuario_id'];
        $_SESSION['academia_id'] = (int) $token['academia_id'];
        $_SESSION['unidade_id'] = $token['unidade_id'] !== null ? (int) $token['unidade_id'] : null;
        $_SESSION['user_type'] = (int) $token['papel'];
        AcademyContext::clear();
        $_SESSION['user_id'] = (int) $token['usuario_id'];
        $_SESSION['academia_id'] = (int) $token['academia_id'];
        $_SESSION['unidade_id'] = $token['unidade_id'] !== null ? (int) $token['unidade_id'] : null;
        $_SESSION['user_type'] = (int) $token['papel'];
        AcademyContext::current();

        Database::getConnection()->prepare('UPDATE api_token SET ultimo_uso_em = CURRENT_TIMESTAMP WHERE idtoken = ?')->execute([self::$tokenId]);
        self::enforceRateLimit(self::$tokenId);
    }

    public static function requireScope(string $scope): void
    {
        if (!in_array($scope, self::$scopes, true) && !in_array('*', self::$scopes, true)) {
            throw new AuthorizationException('Token sem escopo para esta operação.');
        }
    }

    private static function enforceRateLimit(int $tokenId): void
    {
        $minute = gmdate('YmdHi');
        $path = sys_get_temp_dir() . '/gym_genesis_api_' . hash('sha256', $tokenId . '|' . $minute) . '.count';
        $count = is_file($path) ? (int) file_get_contents($path) : 0;
        $count++;
        file_put_contents($path, (string) $count, LOCK_EX);
        if ($count > 120) {
            throw new \App\Exceptions\HttpException(429, 'Limite de requisições excedido.', ['Retry-After' => '60']);
        }
    }
}
