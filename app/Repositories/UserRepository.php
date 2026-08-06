<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Services\Database;
use App\Tenancy\AcademyContext;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('usuario', 'idusuario');
    }

    public function create(string $passwordHash, string $email, UserRole $role): ?int
    {
        return Database::transaction(function () use ($passwordHash, $email, $role): int {
            $stmt = $this->db->prepare("INSERT INTO usuario (senha, email, tipo_usuario, status) VALUES (?, ?, ?, 'ativo')");
            $stmt->execute([$passwordHash, $email, $role->value]);
            $userId = (int) $this->db->lastInsertId();

            $link = $this->db->prepare('INSERT INTO academia_usuario (academia_id, usuario_id, unidade_id, papel, is_principal, ativo) VALUES (?, ?, ?, ?, 1, 1)');
            $link->execute([AcademyContext::id(), $userId, AcademyContext::unitId(), $role->value]);

            return $userId;
        });
    }

    public function findForAuthentication(string $email): ?array
    {
        $sql = "SELECT u.idusuario, u.email, u.senha, u.status,
                       COALESCE((SELECT au.papel FROM academia_usuario au WHERE au.usuario_id = u.idusuario AND au.ativo = 1 ORDER BY au.is_principal DESC, au.created_at ASC LIMIT 1), u.tipo_usuario) AS tipo_usuario
                FROM usuario u WHERE u.email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT idusuario, email, status, last_login_at, created_at, updated_at FROM usuario WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT u.idusuario, u.email, u.status, u.last_login_at, u.created_at, u.updated_at, au.papel, au.ativo AS vinculo_ativo, au.unidade_id FROM usuario u INNER JOIN academia_usuario au ON au.usuario_id = u.idusuario WHERE u.idusuario = ? AND au.academia_id = ? LIMIT 1');
        $stmt->execute([$id, AcademyContext::id()]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function update(int $id, string $email, UserRole $role, bool $active): bool
    {
        if ($this->findById($id) === null) {
            return false;
        }

        return Database::transaction(function () use ($id, $email, $role, $active): bool {
            $stmt = $this->db->prepare('UPDATE usuario SET email = ? WHERE idusuario = ?');
            $stmt->execute([$email, $id]);

            $membership = $this->db->prepare('UPDATE academia_usuario SET papel = ?, ativo = ? WHERE usuario_id = ? AND academia_id = ?');
            return $membership->execute([$role->value, $active ? 1 : 0, $id, AcademyContext::id()]);
        });
    }

    public function updatePasswordHash(int $id, string $passwordHash): bool
    {
        $stmt = $this->db->prepare('UPDATE usuario SET senha = ? WHERE idusuario = ?');
        return $stmt->execute([$passwordHash, $id]);
    }

    public function recordLogin(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE usuario SET last_login_at = CURRENT_TIMESTAMP WHERE idusuario = ?');
        $stmt->execute([$id]);
    }

    public function delete(int $id): bool
    {
        $academyId = AcademyContext::id();

        return Database::transaction(function () use ($id, $academyId): bool {
            $membership = $this->db->prepare('DELETE FROM academia_usuario WHERE usuario_id = ? AND academia_id = ?');
            $membership->execute([$id, $academyId]);

            $remaining = $this->db->prepare('SELECT COUNT(*) FROM academia_usuario WHERE usuario_id = ?');
            $remaining->execute([$id]);
            if ((int) $remaining->fetchColumn() > 0) {
                return true;
            }

            $stmt = $this->db->prepare("UPDATE usuario SET status = 'inativo' WHERE idusuario = ?");
            return $stmt->execute([$id]);
        });
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->prepare('SELECT u.idusuario, u.email, u.status, u.last_login_at, u.created_at, au.papel, au.ativo AS vinculo_ativo, au.unidade_id FROM usuario u INNER JOIN academia_usuario au ON au.usuario_id = u.idusuario WHERE au.academia_id = ? ORDER BY u.idusuario');
        $stmt->execute([AcademyContext::id()]);
        return $stmt->fetchAll();
    }
}
