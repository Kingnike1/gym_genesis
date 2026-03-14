<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class AlunoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct("aluno", "idaluno");
    }

    public function create(int $usuarioId, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $peso, float $altura, string $objetivo): ?int
    {
        $sql = "INSERT INTO aluno (usuario_id, nome, cpf, rg, data_nascimento, sexo, telefone, peso, altura, objetivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $peso, $altura, $objetivo]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $peso, float $altura, string $objetivo): bool
    {
        $sql = "UPDATE aluno SET nome=?, cpf=?, rg=?, data_nascimento=?, sexo=?, telefone=?, peso=?, altura=?, objetivo=? WHERE idaluno=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $peso, $altura, $objetivo, $id]);
    }

    public function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM aluno WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
