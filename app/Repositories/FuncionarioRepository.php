<?php

namespace App\Repositories;

class FuncionarioRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('funcionario', 'idfuncionario', true);
    }

    public function create(int $usuarioId, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $salario, string $cargo, string $dataAdmissao): ?int
    {
        $sql = 'INSERT INTO funcionario (usuario_id, nome, cpf, rg, data_nascimento, sexo, telefone, salario, cargo, data_admissao, academia_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $salario, $cargo, $dataAdmissao, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nome, string $cpf, string $rg, string $dataNascimento, string $sexo, string $telefone, float $salario, string $cargo, string $dataAdmissao): bool
    {
        $sql = 'UPDATE funcionario SET nome=?, cpf=?, rg=?, data_nascimento=?, sexo=?, telefone=?, salario=?, cargo=?, data_admissao=? WHERE idfuncionario=? AND academia_id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nome, $cpf, $rg, $dataNascimento, $sexo, $telefone, $salario, $cargo, $dataAdmissao, $id, $this->academyId()]);
    }

    public function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM funcionario WHERE usuario_id = ? AND academia_id = ?');
        $stmt->execute([$usuarioId, $this->academyId()]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
