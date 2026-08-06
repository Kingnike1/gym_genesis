<?php

namespace App\Repositories;

use App\Services\Database;
use App\Tenancy\AcademyContext;

final class AcademyRepository
{
    public function listForUser(int $userId): array
    {
        $sql = 'SELECT a.idacademia, a.nome, a.nome_fantasia, a.status, au.unidade_id, au.is_principal
                FROM academias a
                INNER JOIN academia_usuario au ON au.academia_id = a.idacademia
                WHERE au.usuario_id = ? AND au.ativo = 1
                ORDER BY au.is_principal DESC, a.nome';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function current(): ?array
    {
        $stmt = Database::getConnection()->prepare('SELECT * FROM academias WHERE idacademia = ? LIMIT 1');
        $stmt->execute([AcademyContext::id()]);
        $academy = $stmt->fetch();
        return $academy ?: null;
    }

    public function updateCurrent(string $nome, ?string $nomeFantasia, ?string $cnpj, ?string $telefone, ?string $email, ?string $logo, ?array $configuracoes): bool
    {
        $stmt = Database::getConnection()->prepare('UPDATE academias SET nome=?, nome_fantasia=?, cnpj=?, telefone=?, email=?, logo=?, configuracoes=? WHERE idacademia=?');
        return $stmt->execute([
            $nome,
            $nomeFantasia,
            $cnpj,
            $telefone,
            $email,
            $logo,
            $configuracoes !== null ? json_encode($configuracoes, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : null,
            AcademyContext::id(),
        ]);
    }

    public function listCurrentUnits(): array
    {
        $stmt = Database::getConnection()->prepare('SELECT * FROM unidades WHERE academia_id = ? ORDER BY nome');
        $stmt->execute([AcademyContext::id()]);
        return $stmt->fetchAll();
    }
}
