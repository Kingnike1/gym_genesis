<?php

namespace App\Repositories;

class PlanoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('plano', 'idplano', true);
    }

    public function create(string $tipo, float $preco, string $descricao, int $duriasDias): ?int
    {
        $sql = 'INSERT INTO plano (tipo, preco, descricao, duriasDias, academia_id) VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipo, $preco, $descricao, $duriasDias, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $tipo, float $preco, string $descricao, int $duriasDias): bool
    {
        $sql = 'UPDATE plano SET tipo=?, preco=?, descricao=?, duriasDias=? WHERE idplano=? AND academia_id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$tipo, $preco, $descricao, $duriasDias, $id, $this->academyId()]);
    }

    public function findByTipo(string $tipo): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM plano WHERE tipo = ? AND academia_id = ?');
        $stmt->execute([$tipo, $this->academyId()]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
