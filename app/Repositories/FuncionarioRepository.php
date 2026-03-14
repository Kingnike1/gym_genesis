<?php

namespace App\Repositories;

use App\Services\Database;
use PDO;

class FuncionarioRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct("funcionario", "idfuncionario");
    }

    public function create(int $usuarioId, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $salario, string $cargo, string $dataAdmissao): ?int
    {
        $sql = "INSERT INTO funcionario (usuario_id, nome, cpf, rg, data_nascimento, sexo, telefone, salario, cargo, data_admissao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $salario, $cargo, $dataAdmissao]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $salario, string $cargo, string $dataAdmissao): bool
    {
        $sql = "UPDATE funcionario SET nome=?, cpf=?, rg=?, data_nascimento=?, sexo=?, telefone=?, salario=?, cargo=?, data_admissao=? WHERE idfuncionario=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $salario, $cargo, $dataAdmissao, $id]);
    }

    public function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM funcionario WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
