<?php

namespace App\Services;

use App\Repositories\ProfessorRepository;
use InvalidArgumentException;

class ProfessorService
{
    public function __construct(private readonly ProfessorRepository $repository)
    {
    }

    public function create(array $data): int
    {
        $nome = trim((string) ($data['nome'] ?? ''));
        $cref = strtoupper(trim((string) ($data['cref'] ?? '')));
        if ($nome === '' || $cref === '') {
            throw new InvalidArgumentException('Nome e CREF são obrigatórios.');
        }

        $id = $this->repository->create(
            (int) ($data['usuario_id'] ?? 0),
            $nome,
            $cref,
            $this->nullable($data['telefone'] ?? null),
            $this->nullable($data['bio'] ?? null),
        );

        $this->repository->replaceUnits($id, $data['unidades'] ?? []);
        $this->repository->replaceSpecialties($id, $data['especialidades'] ?? []);
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $nome = trim((string) ($data['nome'] ?? ''));
        $cref = strtoupper(trim((string) ($data['cref'] ?? '')));
        $status = (string) ($data['status'] ?? 'ativo');
        if ($nome === '' || $cref === '') {
            throw new InvalidArgumentException('Nome e CREF são obrigatórios.');
        }
        if (!in_array($status, ['ativo', 'inativo', 'suspenso'], true)) {
            throw new InvalidArgumentException('Status de professor inválido.');
        }

        $updated = $this->repository->updateProfile($id, $nome, $cref, $this->nullable($data['telefone'] ?? null), $this->nullable($data['bio'] ?? null), $status);
        if ($updated) {
            $this->repository->replaceUnits($id, $data['unidades'] ?? []);
            $this->repository->replaceSpecialties($id, $data['especialidades'] ?? []);
        }
        return $updated;
    }

    public function linkStudent(int $professorId, int $studentId): bool
    {
        return $this->repository->linkStudent($professorId, $studentId);
    }

    public function unlinkStudent(int $professorId, int $studentId): bool
    {
        return $this->repository->unlinkStudent($professorId, $studentId);
    }

    public function students(int $professorId): array
    {
        return $this->repository->students($professorId);
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }
}
