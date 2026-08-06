<?php

namespace App\Tenancy;

use App\Services\Database;

final class AcademyAudit
{
    public static function record(string $acao, string $recurso, string|int|null $recursoId = null, array $contexto = []): void
    {
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $statement = Database::getConnection()->prepare(
            'INSERT INTO auditoria_academia (academia_id, usuario_id, acao, recurso, recurso_id, contexto) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            AcademyContext::id(),
            $userId,
            $acao,
            $recurso,
            $recursoId !== null ? (string) $recursoId : null,
            $contexto !== [] ? json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : null,
        ]);
    }
}
