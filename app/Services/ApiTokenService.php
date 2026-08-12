<?php

declare(strict_types=1);

namespace App\Services;

use App\Tenancy\AcademyContext;
use DateTimeImmutable;

final class ApiTokenService
{
    public function issue(int $userId, string $name, array $scopes, ?DateTimeImmutable $expiresAt = null): string
    {
        $rawToken = bin2hex(random_bytes(32));
        $hash = hash('sha256', $rawToken);
        $normalizedScopes = array_values(array_unique(array_filter(array_map('strval', $scopes))));
        if ($normalizedScopes === []) {
            throw new \InvalidArgumentException('Ao menos um escopo é obrigatório.');
        }

        $stmt = Database::getConnection()->prepare('INSERT INTO api_token (academia_id, usuario_id, nome, token_hash, scopes, expira_em) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            AcademyContext::id(),
            $userId,
            trim($name),
            $hash,
            json_encode($normalizedScopes, JSON_THROW_ON_ERROR),
            $expiresAt?->format('Y-m-d H:i:s'),
        ]);

        return $rawToken;
    }

    public function revoke(int $tokenId): bool
    {
        $stmt = Database::getConnection()->prepare('UPDATE api_token SET revogado_em = CURRENT_TIMESTAMP WHERE idtoken = ? AND academia_id = ? AND revogado_em IS NULL');
        return $stmt->execute([$tokenId, AcademyContext::id()]);
    }
}
