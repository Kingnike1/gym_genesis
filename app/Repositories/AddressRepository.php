<?php

namespace App\Repositories;

class AddressRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('endereco', 'idendereco', true);
    }

    public function create(int $entityId, string $cep, string $rua, string $numero, ?string $complemento, string $bairro, string $cidade, string $estado, int $entityType): ?int
    {
        $entityColumn = ($entityType === 1 || $entityType === 2) ? 'usuario_id' : 'funcionario_id';
        $sql = "INSERT INTO endereco ({$entityColumn}, cep, rua, numero, complemento, bairro, cidade, estado, academia_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$entityId, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $this->academyId()]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $entityId, string $cep, string $rua, string $numero, ?string $complemento, string $bairro, string $cidade, string $estado, int $entityType): bool
    {
        $entityColumn = ($entityType === 1 || $entityType === 2) ? 'usuario_id' : 'funcionario_id';
        $sql = "UPDATE endereco SET cep=?, rua=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE {$entityColumn}=? AND academia_id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $entityId, $this->academyId()]);
    }

    public function deleteByEntity(int $entityId, int $entityType): bool
    {
        $entityColumn = ($entityType === 1 || $entityType === 2) ? 'usuario_id' : 'funcionario_id';
        $sql = "DELETE FROM endereco WHERE {$entityColumn}=? AND academia_id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$entityId, $this->academyId()]);
    }

    public function findByEntity(int $entityId, int $entityType): ?array
    {
        $entityColumn = ($entityType === 1 || $entityType === 2) ? 'usuario_id' : 'funcionario_id';
        $sql = "SELECT * FROM endereco WHERE {$entityColumn}=? AND academia_id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$entityId, $this->academyId()]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAllAddresses(): array
    {
        $sql = 'SELECT e.idendereco, u.email AS email_usuario, f.nome AS nome_funcionario, e.cep, e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado
                FROM endereco e
                LEFT JOIN usuario u ON e.usuario_id = u.idusuario
                LEFT JOIN funcionario f ON e.funcionario_id = f.idfuncionario AND f.academia_id = e.academia_id
                WHERE e.academia_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->academyId()]);
        return $stmt->fetchAll();
    }
}
