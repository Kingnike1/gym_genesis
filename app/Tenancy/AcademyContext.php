<?php

namespace App\Tenancy;

use App\Security\SessionManager;
use App\Services\Database;
use PDO;
use RuntimeException;

final class AcademyContext
{
    private static ?array $current = null;

    public static function current(): array
    {
        SessionManager::start();

        if (self::$current !== null) {
            return self::$current;
        }

        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
        if ($userId <= 0) {
            throw new RuntimeException('Usuário autenticado é obrigatório para resolver a academia atual.');
        }

        $requestedAcademyId = isset($_SESSION['academia_id']) ? (int) $_SESSION['academia_id'] : null;
        self::$current = self::loadMembership($userId, $requestedAcademyId);

        if (self::$current === null) {
            self::$current = self::loadPrincipalMembership($userId);
        }

        if (self::$current === null) {
            throw new RuntimeException('Usuário não possui vínculo ativo com uma academia.');
        }

        $_SESSION['academia_id'] = (int) self::$current['academia_id'];
        $_SESSION['unidade_id'] = self::$current['unidade_id'] !== null ? (int) self::$current['unidade_id'] : null;

        return self::$current;
    }

    public static function id(): int
    {
        return (int) self::current()['academia_id'];
    }

    public static function unitId(): ?int
    {
        $unitId = self::current()['unidade_id'];
        return $unitId !== null ? (int) $unitId : null;
    }

    public static function select(int $academyId): void
    {
        SessionManager::start();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
        $membership = $userId > 0 ? self::loadMembership($userId, $academyId) : null;

        if ($membership === null) {
            throw new RuntimeException('Usuário não possui acesso à academia informada.');
        }

        self::$current = $membership;
        $_SESSION['academia_id'] = (int) $membership['academia_id'];
        $_SESSION['unidade_id'] = $membership['unidade_id'] !== null ? (int) $membership['unidade_id'] : null;
    }

    public static function clear(): void
    {
        self::$current = null;
        unset($_SESSION['academia_id'], $_SESSION['unidade_id']);
    }

    private static function loadMembership(int $userId, ?int $academyId): ?array
    {
        if ($academyId === null || $academyId <= 0) {
            return null;
        }

        $sql = 'SELECT au.academia_id, au.unidade_id, a.nome, a.nome_fantasia, a.status
                FROM academia_usuario au
                INNER JOIN academias a ON a.idacademia = au.academia_id
                WHERE au.usuario_id = :usuario_id
                  AND au.academia_id = :academia_id
                  AND au.ativo = 1
                  AND a.status = \'ativa\'
                LIMIT 1';

        $statement = Database::getConnection()->prepare($sql);
        $statement->execute(['usuario_id' => $userId, 'academia_id' => $academyId]);
        $membership = $statement->fetch(PDO::FETCH_ASSOC);

        return $membership ?: null;
    }

    private static function loadPrincipalMembership(int $userId): ?array
    {
        $sql = 'SELECT au.academia_id, au.unidade_id, a.nome, a.nome_fantasia, a.status
                FROM academia_usuario au
                INNER JOIN academias a ON a.idacademia = au.academia_id
                WHERE au.usuario_id = :usuario_id
                  AND au.ativo = 1
                  AND a.status = \'ativa\'
                ORDER BY au.is_principal DESC, au.created_at ASC
                LIMIT 1';

        $statement = Database::getConnection()->prepare($sql);
        $statement->execute(['usuario_id' => $userId]);
        $membership = $statement->fetch(PDO::FETCH_ASSOC);

        return $membership ?: null;
    }
}
