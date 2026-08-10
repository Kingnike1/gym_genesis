<?php

namespace App\Repositories;

use App\Middleware\AuthMiddleware;
use App\Services\Database;

class MatriculaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('matricula', 'idmatricula', true);
    }

    public function create(int $alunoId, int $planoId, float $valorContratado, string $dataInicio, ?string $dataFim = null, ?string $proximaCobranca = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO matricula (academia_id, unidade_id, aluno_id, plano_id, valor_contratado, data_inicio, data_fim, proxima_cobranca) SELECT ?, ?, a.idaluno, p.idplano, ?, ?, ?, ? FROM aluno a INNER JOIN plano_comercial p ON p.idplano=? WHERE a.idaluno=? AND a.academia_id=? AND p.academia_id=?');
        $stmt->execute([$this->academyId(), \App\Tenancy\AcademyContext::unitId(), $valorContratado, $dataInicio, $dataFim, $proximaCobranca, $planoId, $alunoId, $this->academyId(), $this->academyId()]);
        if ($stmt->rowCount() !== 1) {
            throw new \DomainException('Aluno e plano devem pertencer à academia atual.');
        }
        return (int) $this->db->lastInsertId();
    }

    public function changeStatus(int $id, string $novoStatus, ?string $motivo = null): bool
    {
        $allowed = ['ativa','suspensa','congelada','cancelada','encerrada','inadimplente'];
        if (!in_array($novoStatus, $allowed, true)) {
            throw new \InvalidArgumentException('Status de matrícula inválido.');
        }
        return Database::transaction(function () use ($id, $novoStatus, $motivo): bool {
            $matricula = $this->find($id);
            if (!$matricula) {
                return false;
            }
            $stmt = $this->db->prepare('UPDATE matricula SET status=?, motivo_status=? WHERE idmatricula=? AND academia_id=?');
            $stmt->execute([$novoStatus, $motivo, $id, $this->academyId()]);
            $hist = $this->db->prepare('INSERT INTO matricula_historico (matricula_id, status_anterior, status_novo, motivo, usuario_id) VALUES (?, ?, ?, ?, ?)');
            $hist->execute([$id, $matricula['status'], $novoStatus, $motivo, AuthMiddleware::getUserId() ?: null]);
            return true;
        });
    }

    public function byStudent(int $alunoId): array
    {
        $stmt = $this->db->prepare('SELECT m.*, p.nome AS plano_nome FROM matricula m INNER JOIN plano_comercial p ON p.idplano=m.plano_id WHERE m.aluno_id=? AND m.academia_id=? ORDER BY m.created_at DESC');
        $stmt->execute([$alunoId, $this->academyId()]);
        return $stmt->fetchAll();
    }
}
