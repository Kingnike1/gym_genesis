<?php

namespace App\Services;

use App\Repositories\UserRepository;

final class PasswordResetService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function issueToken(string $email): ?string
    {
        $user = $this->users->findByEmail(strtolower(trim($email)));
        if (!$user || ($user['status'] ?? 'inativo') !== 'ativo') {
            return null;
        }

        $raw = bin2hex(random_bytes(32));
        $hash = hash('sha256', $raw);
        $pdo = Database::getConnection();
        $pdo->prepare('UPDATE password_reset_token SET used_at=NOW() WHERE usuario_id=? AND used_at IS NULL')->execute([(int) $user['idusuario']]);
        $stmt = $pdo->prepare('INSERT INTO password_reset_token (usuario_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))');
        $stmt->execute([(int) $user['idusuario'], $hash]);
        return $raw;
    }

    public function reset(string $rawToken, string $newPassword): bool
    {
        if (strlen($newPassword) < 10) {
            throw new \InvalidArgumentException('A nova senha deve possuir pelo menos 10 caracteres.');
        }
        $hash = hash('sha256', trim($rawToken));
        return Database::transaction(function () use ($hash, $newPassword): bool {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT idtoken, usuario_id FROM password_reset_token WHERE token_hash=? AND used_at IS NULL AND expires_at > NOW() FOR UPDATE');
            $stmt->execute([$hash]);
            $token = $stmt->fetch();
            if (!$token) {
                return false;
            }
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE usuario SET senha=?, password_changed_at=NOW(), session_version=session_version+1 WHERE idusuario=?');
            $update->execute([$passwordHash, (int) $token['usuario_id']]);
            $pdo->prepare('UPDATE password_reset_token SET used_at=NOW() WHERE idtoken=?')->execute([(int) $token['idtoken']]);
            $pdo->prepare('UPDATE password_reset_token SET used_at=NOW() WHERE usuario_id=? AND used_at IS NULL')->execute([(int) $token['usuario_id']]);
            return true;
        });
    }
}
