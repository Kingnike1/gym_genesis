<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct("usuario", "idusuario");
    }


    public function create(string $passwordHash, string $email, int $userType): ?int
    {
        $stmt = $this->db->prepare("INSERT INTO usuario (senha, email, tipo_usuario) VALUES (?, ?, ?)");
        $stmt->execute([$passwordHash, $email, $userType]);
        return (int)$this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE idusuario = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function update(int $id, string $passwordHash, string $email, int $userType): bool
    {
        $stmt = $this->db->prepare("UPDATE usuario SET senha = ?, email = ?, tipo_usuario = ? WHERE idusuario = ?");
        return $stmt->execute([$passwordHash, $email, $userType, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM usuario WHERE idusuario = ?");
        return $stmt->execute([$id]);
    }

    public function getAllUsers(): array
    {
        return $this->all();
    }
}
