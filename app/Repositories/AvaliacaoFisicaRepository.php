<?php

namespace App\Repositories;

use App\Services\Database;

class AvaliacaoFisicaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('avaliacao_fisica_registro', 'idavaliacao', true);
    }

    public function create(int $alunoId, ?int $responsavelUsuarioId, float $peso, float $alturaCm, ?float $percentualGordura, ?string $pressaoArterial, ?string $observacoes, array $medidas = []): int
    {
        return Database::transaction(function () use ($alunoId, $responsavelUsuarioId, $peso, $alturaCm, $percentualGordura, $pressaoArterial, $observacoes, $medidas): int {
            $alturaM = $alturaCm / 100;
            $imc = round($peso / ($alturaM * $alturaM), 2);
            $stmt = $this->db->prepare('INSERT INTO avaliacao_fisica_registro (academia_id, aluno_id, responsavel_usuario_id, data_avaliacao, peso, altura, imc, percentual_gordura, pressao_arterial, observacoes) VALUES (?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$this->academyId(), $alunoId, $responsavelUsuarioId, $peso, $alturaCm, $imc, $percentualGordura, $pressaoArterial, $observacoes]);
            $id = (int) $this->db->lastInsertId();
            $insert = $this->db->prepare('INSERT INTO avaliacao_fisica_medida (avaliacao_id, nome, valor, unidade) VALUES (?, ?, ?, ?)');
            foreach ($medidas as $medida) {
                $insert->execute([$id, trim((string) $medida['nome']), (float) $medida['valor'], trim((string) ($medida['unidade'] ?? 'cm'))]);
            }
            return $id;
        });
    }

    public function findByAlunoId(int $alunoId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM avaliacao_fisica_registro WHERE aluno_id = ? AND academia_id = ? ORDER BY data_avaliacao DESC, idavaliacao DESC');
        $stmt->execute([$alunoId, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findLatestByAlunoId(int $alunoId): ?array
    {
        $rows = $this->findByAlunoId($alunoId);
        return $rows[0] ?? null;
    }

    public function measurements(int $avaliacaoId): array
    {
        $stmt = $this->db->prepare('SELECT m.* FROM avaliacao_fisica_medida m INNER JOIN avaliacao_fisica_registro a ON a.idavaliacao=m.avaliacao_id WHERE m.avaliacao_id=? AND a.academia_id=? ORDER BY m.nome');
        $stmt->execute([$avaliacaoId, $this->academyId()]);
        return $stmt->fetchAll();
    }
}
