<?php

namespace App\Repositories;

use App\Services\Database;
use App\Tenancy\AcademyContext;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('usuario', 'idusuario');
    }

    public function create(string $passwordHash, string $email, int $userType): ?int
    {
        return Database::transaction(function () use ($passwordHash, $email, $userType): int {
            $stmt = $this->db->prepare('INSERT INTO usuario (senha, email, tipo_usuario) VALUES (?, ?, ?)');
            $stmt->execute([$passwordHash, $email, $userType]);
            $userId = (int) $this->db->lastInsertId();

            $link = $this->db->prepare('INSERT INTO academia_usuario (academia_id, usuario_id, unidade_id, is_principal, ativo) VALUES (?, ?, ?, 1, 1)');
            $link->execute([AcademyContext::id(), $userId, AcademyContext::unitId()]);

            return $userId;
        });
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuario WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT u.* FROM usuario u INNER JOIN academia_usuario au ON au.usuario_id = u.idusuario WHERE u.idusuario = ? AND au.academia_id = ? AND au.ativo = 1 LIMIT 1');
        $stmt->execute([$id, AcademyContext::id()]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function update(int $id, string $passwordHash, string $email, int $userType): bool
    {
        if ($this->findById($id) === null) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE usuario SET senha = ?, email = ?, tipo_usuario = ? WHERE idusuario = ?');
        return $stmt->execute([$passwordHash, $email, $userType, $id]);
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

            $stmt = $this->db->prepare('DELETE FROM usuario WHERE idusuario = ?');
            return $stmt->execute([$id]);
        });
    }

    public function getAllUsers(): array
    {
        $stmt = $this->db->prepare('SELECT u.* FROM usuario u INNER JOIN academia_usuario au ON au.usuario_id = u.idusuario WHERE au.academia_id = ? AND au.ativo = 1 ORDER BY u.idusuario');
        $stmt->execute([AcademyContext::id()]);
        return $stmt->fetchAll();
    }
}
