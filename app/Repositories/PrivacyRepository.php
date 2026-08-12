<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Tenancy\AcademyContext;

final class PrivacyRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('solicitacao_titular', 'idsolicitacao', true);
    }

    public function recordConsent(int $userId, string $purpose, string $termsVersion, string $source = 'web'): int
    {
        $stmt = $this->db->prepare('INSERT INTO consentimento_privacidade (academia_id, usuario_id, finalidade, versao_termo, origem) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([AcademyContext::id(), $userId, $purpose, $termsVersion, $source]);
        return (int) $this->db->lastInsertId();
    }

    public function revokeConsent(int $userId, string $purpose): bool
    {
        $stmt = $this->db->prepare('UPDATE consentimento_privacidade SET revogado_em = CURRENT_TIMESTAMP WHERE academia_id = ? AND usuario_id = ? AND finalidade = ? AND revogado_em IS NULL');
        return $stmt->execute([AcademyContext::id(), $userId, $purpose]);
    }

    public function openRequest(int $userId, string $type, ?string $details = null): int
    {
        $allowed = ['acesso', 'correcao', 'exportacao', 'eliminacao', 'anonimizacao', 'revogacao'];
        if (!in_array($type, $allowed, true)) {
            throw new \InvalidArgumentException('Tipo de solicitação inválido.');
        }

        $stmt = $this->db->prepare('INSERT INTO solicitacao_titular (academia_id, usuario_id, tipo, detalhes) VALUES (?, ?, ?, ?)');
        $stmt->execute([AcademyContext::id(), $userId, $type, $details]);
        return (int) $this->db->lastInsertId();
    }

    public function requestsByUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT idsolicitacao, tipo, status, criada_em, concluida_em FROM solicitacao_titular WHERE academia_id = ? AND usuario_id = ? ORDER BY criada_em DESC');
        $stmt->execute([AcademyContext::id(), $userId]);
        return $stmt->fetchAll();
    }
}
