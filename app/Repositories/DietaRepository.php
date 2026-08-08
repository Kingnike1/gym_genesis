<?php

namespace App\Repositories;

use App\Services\Database;
use App\Tenancy\AcademyContext;
use PDO;

class DietaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('plano_alimentar', 'idplano_alimentar', true);
    }

    public function createPlan(
        int $alunoId,
        int $responsavelUsuarioId,
        string $nome,
        string $objetivo,
        string $qualificacao,
        ?string $registroProfissional,
        string $dataInicio,
        ?string $dataFim,
        ?string $observacoes,
        array $refeicoes
    ): int {
        return Database::transaction(function () use ($alunoId, $responsavelUsuarioId, $nome, $objetivo, $qualificacao, $registroProfissional, $dataInicio, $dataFim, $observacoes, $refeicoes): int {
            $this->assertStudentBelongsToAcademy($alunoId);
            $this->assertResponsibleBelongsToAcademy($responsavelUsuarioId);

            $stmt = $this->db->prepare('INSERT INTO plano_alimentar (academia_id, aluno_id, responsavel_usuario_id, nome, objetivo, observacoes, qualificacao_responsavel, registro_profissional, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([AcademyContext::id(), $alunoId, $responsavelUsuarioId, $nome, $objetivo, $observacoes, $qualificacao, $registroProfissional, $dataInicio, $dataFim]);
            $planId = (int) $this->db->lastInsertId();

            $this->replaceMeals($planId, $refeicoes);
            $this->recordHistory($planId, $responsavelUsuarioId, 1, 'criado');

            return $planId;
        });
    }

    public function updatePlan(int $id, int $responsavelUsuarioId, array $data, array $refeicoes): bool
    {
        $current = $this->find($id);
        if (!$current) {
            return false;
        }

        return Database::transaction(function () use ($id, $responsavelUsuarioId, $data, $refeicoes, $current): bool {
            $nextVersion = ((int) $current['versao']) + 1;
            $stmt = $this->db->prepare('UPDATE plano_alimentar SET nome = ?, objetivo = ?, observacoes = ?, qualificacao_responsavel = ?, registro_profissional = ?, data_inicio = ?, data_fim = ?, status = ?, versao = ? WHERE idplano_alimentar = ? AND academia_id = ?');
            $stmt->execute([
                $data['nome'], $data['objetivo'], $data['observacoes'], $data['qualificacao_responsavel'],
                $data['registro_profissional'], $data['data_inicio'], $data['data_fim'], $data['status'],
                $nextVersion, $id, AcademyContext::id(),
            ]);

            $this->replaceMeals($id, $refeicoes);
            $this->recordHistory($id, $responsavelUsuarioId, $nextVersion, 'atualizado');
            return true;
        });
    }

    public function findDetailed(int $id): ?array
    {
        $plan = $this->find($id);
        if (!$plan) {
            return null;
        }

        $meals = $this->db->prepare('SELECT * FROM plano_alimentar_refeicao WHERE plano_alimentar_id = ? ORDER BY ordem, idrefeicao');
        $meals->execute([$id]);
        $plan['refeicoes'] = $meals->fetchAll(PDO::FETCH_ASSOC);

        $items = $this->db->prepare('SELECT * FROM plano_alimentar_item WHERE refeicao_id = ? ORDER BY ordem, iditem');
        foreach ($plan['refeicoes'] as &$meal) {
            $items->execute([(int) $meal['idrefeicao']]);
            $meal['itens'] = $items->fetchAll(PDO::FETCH_ASSOC);
        }

        return $plan;
    }

    public function findByAlunoId(int $alunoId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM plano_alimentar WHERE aluno_id = ? AND academia_id = ? ORDER BY created_at DESC');
        $stmt->execute([$alunoId, AcademyContext::id()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByResponsibleUserId(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM plano_alimentar WHERE responsavel_usuario_id = ? AND academia_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId, AcademyContext::id()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function history(int $id): array
    {
        if (!$this->find($id)) {
            return [];
        }
        $stmt = $this->db->prepare('SELECT * FROM plano_alimentar_historico WHERE plano_alimentar_id = ? ORDER BY created_at DESC, idhistorico DESC');
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function replaceMeals(int $planId, array $meals): void
    {
        $delete = $this->db->prepare('DELETE FROM plano_alimentar_refeicao WHERE plano_alimentar_id = ?');
        $delete->execute([$planId]);

        $mealStmt = $this->db->prepare('INSERT INTO plano_alimentar_refeicao (plano_alimentar_id, nome, horario, ordem, observacoes) VALUES (?, ?, ?, ?, ?)');
        $itemStmt = $this->db->prepare('INSERT INTO plano_alimentar_item (refeicao_id, descricao, quantidade, unidade, substituicoes, ordem) VALUES (?, ?, ?, ?, ?, ?)');

        foreach ($meals as $mealIndex => $meal) {
            $mealStmt->execute([$planId, trim((string) ($meal['nome'] ?? 'Refeição')), $meal['horario'] ?? null, $mealIndex + 1, $meal['observacoes'] ?? null]);
            $mealId = (int) $this->db->lastInsertId();
            foreach (($meal['itens'] ?? []) as $itemIndex => $item) {
                $description = trim((string) ($item['descricao'] ?? ''));
                if ($description === '') {
                    continue;
                }
                $itemStmt->execute([$mealId, $description, $item['quantidade'] ?? null, $item['unidade'] ?? null, $item['substituicoes'] ?? null, $itemIndex + 1]);
            }
        }
    }

    private function recordHistory(int $planId, int $userId, int $version, string $event): void
    {
        $snapshot = $this->findDetailed($planId);
        $stmt = $this->db->prepare('INSERT INTO plano_alimentar_historico (plano_alimentar_id, usuario_id, versao, evento, snapshot_json) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$planId, $userId, $version, $event, json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    }

    private function assertStudentBelongsToAcademy(int $studentId): void
    {
        $stmt = $this->db->prepare("SELECT 1 FROM aluno WHERE idaluno = ? AND academia_id = ? AND status = 'ativo'");
        $stmt->execute([$studentId, AcademyContext::id()]);
        if (!$stmt->fetchColumn()) {
            throw new \InvalidArgumentException('Aluno inválido para a academia atual.');
        }
    }

    private function assertResponsibleBelongsToAcademy(int $userId): void
    {
        $stmt = $this->db->prepare('SELECT 1 FROM academia_usuario WHERE usuario_id = ? AND academia_id = ? AND ativo = 1');
        $stmt->execute([$userId, AcademyContext::id()]);
        if (!$stmt->fetchColumn()) {
            throw new \InvalidArgumentException('Responsável não pertence à academia atual.');
        }
    }
}
