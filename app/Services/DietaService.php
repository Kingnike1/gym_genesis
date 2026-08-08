<?php

namespace App\Services;

use App\Repositories\DietaRepository;

class DietaService
{
    public function __construct(private readonly DietaRepository $dietaRepository)
    {
    }

    public function createDieta(array $data, int $responsavelUsuarioId): int
    {
        $this->validate($data);
        return $this->dietaRepository->createPlan(
            (int) $data['aluno_id'],
            $responsavelUsuarioId,
            trim((string) $data['nome']),
            trim((string) ($data['objetivo'] ?? '')),
            trim((string) $data['qualificacao_responsavel']),
            $this->nullableString($data['registro_profissional'] ?? null),
            (string) $data['data_inicio'],
            $this->nullableString($data['data_fim'] ?? null),
            $this->nullableString($data['observacoes'] ?? null),
            is_array($data['refeicoes'] ?? null) ? $data['refeicoes'] : []
        );
    }

    public function updateDieta(int $id, array $data, int $responsavelUsuarioId): bool
    {
        $this->validate($data, false);
        return $this->dietaRepository->updatePlan($id, $responsavelUsuarioId, [
            'nome' => trim((string) $data['nome']),
            'objetivo' => trim((string) ($data['objetivo'] ?? '')),
            'observacoes' => $this->nullableString($data['observacoes'] ?? null),
            'qualificacao_responsavel' => trim((string) $data['qualificacao_responsavel']),
            'registro_profissional' => $this->nullableString($data['registro_profissional'] ?? null),
            'data_inicio' => (string) $data['data_inicio'],
            'data_fim' => $this->nullableString($data['data_fim'] ?? null),
            'status' => in_array(($data['status'] ?? 'rascunho'), ['rascunho', 'ativo', 'encerrado'], true) ? $data['status'] : 'rascunho',
        ], is_array($data['refeicoes'] ?? null) ? $data['refeicoes'] : []);
    }

    public function getDietaById(int $id): ?array
    {
        return $this->dietaRepository->findDetailed($id);
    }

    public function getDietasByAlunoId(int $alunoId): array
    {
        return $this->dietaRepository->findByAlunoId($alunoId);
    }

    public function getDietasByResponsibleUserId(int $userId): array
    {
        return $this->dietaRepository->findByResponsibleUserId($userId);
    }

    public function getHistorico(int $id): array
    {
        return $this->dietaRepository->history($id);
    }

    public function deleteDieta(int $id): bool
    {
        return $this->dietaRepository->delete($id);
    }

    private function validate(array $data, bool $requireStudent = true): void
    {
        if ($requireStudent && (int) ($data['aluno_id'] ?? 0) <= 0) {
            throw new \InvalidArgumentException('Aluno é obrigatório.');
        }
        if (trim((string) ($data['nome'] ?? '')) === '') {
            throw new \InvalidArgumentException('Nome do plano alimentar é obrigatório.');
        }
        if (trim((string) ($data['qualificacao_responsavel'] ?? '')) === '') {
            throw new \InvalidArgumentException('Qualificação do responsável é obrigatória.');
        }
        if (trim((string) ($data['data_inicio'] ?? '')) === '') {
            throw new \InvalidArgumentException('Data de início é obrigatória.');
        }
        $end = $this->nullableString($data['data_fim'] ?? null);
        if ($end !== null && $end < (string) $data['data_inicio']) {
            throw new \InvalidArgumentException('Data final não pode ser anterior à data inicial.');
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }
}
