<?php

namespace App\Repositories;

class PlanoRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('plano_comercial', 'idplano', true);
    }

    public function create(string $tipo, float $preco, string $descricao, int $duriasDias): ?int
    {
        if ($preco < 0 || $duriasDias <= 0) {
            throw new \InvalidArgumentException('Valor e duração do plano são inválidos.');
        }
        $stmt = $this->db->prepare('INSERT INTO plano_comercial (academia_id, nome, descricao, valor, duracao_dias) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$this->academyId(), trim($tipo), $descricao, $preco, $duriasDias]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $tipo, float $preco, string $descricao, int $duriasDias): bool
    {
        $stmt = $this->db->prepare('UPDATE plano_comercial SET nome=?, valor=?, descricao=?, duracao_dias=? WHERE idplano=? AND academia_id=?');
        return $stmt->execute([trim($tipo), $preco, $descricao, $duriasDias, $id, $this->academyId()]);
    }

    public function findByTipo(string $tipo): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM plano_comercial WHERE nome=? AND academia_id=? LIMIT 1');
        $stmt->execute([$tipo, $this->academyId()]);
        return $stmt->fetch() ?: null;
    }
}
