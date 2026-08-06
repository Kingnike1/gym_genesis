<?php

namespace App\Repositories;

class AvaliacaoFisicaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('avaliacao_fisica', 'idavaliacao', true);
    }

    public function create(float $peso, float $altura, float $imc, ?float $percentualGordura, string $dataAvaliacao, int $usuarioId): ?int
    {
        $sql = 'INSERT INTO avaliacao_fisica (peso, altura, imc, percentual_gordura, data_avaliacao, usuario_id, academia_id) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$peso, $altura, $imc, $percentualGordura, $dataAvaliacao, $usuarioId, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, float $peso, float $altura, float $imc, ?float $percentualGordura, string $dataAvaliacao): bool
    {
        $sql = 'UPDATE avaliacao_fisica SET peso=?, altura=?, imc=?, percentual_gordura=?, data_avaliacao=? WHERE idavaliacao=? AND academia_id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$peso, $altura, $imc, $percentualGordura, $dataAvaliacao, $id, $this->academyId()]);
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM avaliacao_fisica WHERE usuario_id = ? AND academia_id = ? ORDER BY data_avaliacao DESC');
        $stmt->execute([$usuarioId, $this->academyId()]);
        return $stmt->fetchAll();
    }

    public function findLatestByUsuarioId(int $usuarioId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM avaliacao_fisica WHERE usuario_id = ? AND academia_id = ? ORDER BY data_avaliacao DESC LIMIT 1');
        $stmt->execute([$usuarioId, $this->academyId()]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByUsuarioIdAndDate(int $usuarioId, string $dataAvaliacao): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM avaliacao_fisica WHERE usuario_id = ? AND data_avaliacao = ? AND academia_id = ? LIMIT 1');
        $stmt->execute([$usuarioId, $dataAvaliacao, $this->academyId()]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
