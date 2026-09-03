<?php

namespace App\Services;

use App\Repositories\AlunoRepository;
use InvalidArgumentException;

class AlunoService
{
    public function __construct(private readonly AlunoRepository $alunoRepository)
    {
    }

    public function createAluno(array $data): int
    {
        $this->assertValid($data);

        if ($this->alunoRepository->findByMatricula(trim((string) $data['matricula'])) !== null) {
            throw new InvalidArgumentException('Matrícula já utilizada nesta academia.');
        }

        return $this->alunoRepository->create(
            (int) $data['usuario_id'],
            trim((string) $data['nome']),
            trim((string) $data['matricula']),
            $this->nullable($data['cpf'] ?? null),
            $this->nullable($data['rg'] ?? null),
            $this->nullable($data['data_nascimento'] ?? null),
            $this->nullable($data['sexo'] ?? null),
            $this->nullable($data['telefone'] ?? null),
            $this->nullable($data['telefone_emergencia'] ?? null),
            $this->nullable($data['contato_emergencia'] ?? null),
            $this->nullable($data['objetivo'] ?? null),
            $this->nullable($data['observacoes'] ?? null),
            $this->normalizeStatus((string) ($data['status'] ?? 'ativo')),
        );
    }

    public function updateAluno(int $id, array $data): bool
    {
        $this->assertValid($data, false);

        return $this->alunoRepository->updateProfile(
            $id,
            trim((string) $data['nome']),
            $this->nullable($data['cpf'] ?? null),
            $this->nullable($data['rg'] ?? null),
            $this->nullable($data['data_nascimento'] ?? null),
            $this->nullable($data['sexo'] ?? null),
            $this->nullable($data['telefone'] ?? null),
            $this->nullable($data['telefone_emergencia'] ?? null),
            $this->nullable($data['contato_emergencia'] ?? null),
            $this->nullable($data['objetivo'] ?? null),
            $this->nullable($data['observacoes'] ?? null),
            $this->normalizeStatus((string) ($data['status'] ?? 'ativo')),
        );
    }

    public function getAlunoById(int $id): ?array
    {
        return $this->alunoRepository->find($id);
    }

    public function getAlunoByUsuarioId(int $usuarioId): ?array
    {
        return $this->alunoRepository->findByUsuarioId($usuarioId);
    }

    public function getAllAlunos(): array
    {
        return $this->alunoRepository->all();
    }

    public function search(string $term = '', ?string $status = null, int $page = 1, int $perPage = 25): array
    {
        $perPage = max(1, min(100, $perPage));
        return $this->alunoRepository->search($term, $status, $perPage, max(0, $page - 1) * $perPage);
    }

    public function changeStatus(int $id, string $status): bool
    {
        return $this->alunoRepository->changeStatus($id, $this->normalizeStatus($status));
    }

    private function assertValid(array $data, bool $requireIdentity = true): void
    {
        if (trim((string) ($data['nome'] ?? '')) === '') {
            throw new InvalidArgumentException('Nome do aluno é obrigatório.');
        }
        if ($requireIdentity && (int) ($data['usuario_id'] ?? 0) <= 0) {
            throw new InvalidArgumentException('Usuário do aluno é obrigatório.');
        }
        if ($requireIdentity && trim((string) ($data['matricula'] ?? '')) === '') {
            throw new InvalidArgumentException('Matrícula é obrigatória.');
        }
    }

    private function normalizeStatus(string $status): string
    {
        if (!in_array($status, ['ativo', 'inativo', 'suspenso'], true)) {
            throw new InvalidArgumentException('Status de aluno inválido.');
        }
        return $status;
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }
}
