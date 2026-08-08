<?php

namespace App\Repositories;

use App\Services\Database;
use App\Tenancy\AcademyContext;

class AlunoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('aluno', 'idaluno', true);
    }

    public function create(
        int $usuarioId,
        string $nome,
        string $matricula,
        ?string $cpf = null,
        ?string $rg = null,
        ?string $dataNascimento = null,
        ?string $sexo = null,
        ?string $telefone = null,
        ?string $telefoneEmergencia = null,
        ?string $contatoEmergencia = null,
        ?string $objetivo = null,
        ?string $observacoes = null,
        string $status = 'ativo'
    ): int {
        $sql = 'INSERT INTO aluno (
                    academia_id, unidade_id, usuario_id, matricula, nome, cpf, rg,
                    data_nascimento, sexo, telefone, telefone_emergencia,
                    contato_emergencia, objetivo, observacoes, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            AcademyContext::id(),
            AcademyContext::unitId(),
            $usuarioId,
            $matricula,
            $nome,
            $cpf,
            $rg,
            $dataNascimento,
            $sexo,
            $telefone,
            $telefoneEmergencia,
            $contatoEmergencia,
            $objetivo,
            $observacoes,
            $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(
        int $id,
        string $nome,
        ?string $cpf,
        ?string $rg,
        ?string $dataNascimento,
        ?string $sexo,
        ?string $telefone,
        ?string $telefoneEmergencia,
        ?string $contatoEmergencia,
        ?string $objetivo,
        ?string $observacoes,
        string $status
    ): bool {
        $sql = 'UPDATE aluno SET nome = ?, cpf = ?, rg = ?, data_nascimento = ?, sexo = ?, telefone = ?,
                    telefone_emergencia = ?, contato_emergencia = ?, objetivo = ?, observacoes = ?, status = ?
                WHERE idaluno = ? AND academia_id = ?';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $nome,
            $cpf,
            $rg,
            $dataNascimento,
            $sexo,
            $telefone,
            $telefoneEmergencia,
            $contatoEmergencia,
            $objetivo,
            $observacoes,
            $status,
            $id,
            AcademyContext::id(),
        ]);
    }

    public function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM aluno WHERE usuario_id = ? AND academia_id = ? LIMIT 1');
        $stmt->execute([$usuarioId, AcademyContext::id()]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findByMatricula(string $matricula): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM aluno WHERE matricula = ? AND academia_id = ? LIMIT 1');
        $stmt->execute([$matricula, AcademyContext::id()]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function search(string $term = '', ?string $status = null, int $limit = 50, int $offset = 0): array
    {
        $conditions = ['academia_id = :academia_id'];
        $params = ['academia_id' => AcademyContext::id()];

        if ($term !== '') {
            $conditions[] = '(nome LIKE :term OR matricula LIKE :term OR cpf LIKE :term)';
            $params['term'] = '%' . $term . '%';
        }

        if ($status !== null && $status !== '') {
            $conditions[] = 'status = :status';
            $params['status'] = $status;
        }

        $sql = 'SELECT * FROM aluno WHERE ' . implode(' AND ', $conditions) . ' ORDER BY nome LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', max(1, min(100, $limit)), \PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function changeStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE aluno SET status = ? WHERE idaluno = ? AND academia_id = ?');
        return $stmt->execute([$status, $id, AcademyContext::id()]);
    }
}
