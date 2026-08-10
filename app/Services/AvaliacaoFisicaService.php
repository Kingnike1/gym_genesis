<?php

namespace App\Services;

use App\Repositories\AvaliacaoFisicaRepository;

class AvaliacaoFisicaService
{
    public function __construct(private AvaliacaoFisicaRepository $avaliacaoRepository)
    {
    }

    public function createAvaliacao(int $alunoId, ?int $responsavelUsuarioId, float $peso, float $alturaCm, ?float $percentualGordura = null, ?string $pressaoArterial = null, ?string $observacoes = null, array $medidas = []): int
    {
        if ($peso <= 0 || $alturaCm <= 0) {
            throw new \InvalidArgumentException('Peso e altura devem ser maiores que zero.');
        }
        if ($percentualGordura !== null && ($percentualGordura < 0 || $percentualGordura > 100)) {
            throw new \InvalidArgumentException('Percentual de gordura inválido.');
        }
        return $this->avaliacaoRepository->create($alunoId, $responsavelUsuarioId, $peso, $alturaCm, $percentualGordura, $pressaoArterial, $observacoes, $medidas);
    }

    public function getAvaliacaoById(int $id): ?array
    {
        return $this->avaliacaoRepository->find($id);
    }

    public function getAvaliacoesByAlunoId(int $alunoId): array
    {
        return $this->avaliacaoRepository->findByAlunoId($alunoId);
    }

    public function getLatestAvaliacaoByAlunoId(int $alunoId): ?array
    {
        return $this->avaliacaoRepository->findLatestByAlunoId($alunoId);
    }

    public function measurements(int $avaliacaoId): array
    {
        return $this->avaliacaoRepository->measurements($avaliacaoId);
    }

    public function calculateProgress(int $alunoId): array
    {
        $avaliacoes = array_reverse($this->getAvaliacoesByAlunoId($alunoId));
        if (count($avaliacoes) < 2) {
            return ['totalAvaliacoes' => count($avaliacoes), 'pesoInicial' => null, 'pesoAtual' => null, 'variacao' => 0.0, 'percentualVariacao' => 0.0];
        }
        $primeira = $avaliacoes[0];
        $ultima = $avaliacoes[array_key_last($avaliacoes)];
        $pesoInicial = (float) $primeira['peso'];
        $pesoAtual = (float) $ultima['peso'];
        $variacao = $pesoAtual - $pesoInicial;
        return [
            'totalAvaliacoes' => count($avaliacoes),
            'pesoInicial' => $pesoInicial,
            'pesoAtual' => $pesoAtual,
            'variacao' => round($variacao, 2),
            'percentualVariacao' => $pesoInicial > 0 ? round(($variacao / $pesoInicial) * 100, 2) : 0.0,
            'imcInicial' => (float) $primeira['imc'],
            'imcAtual' => (float) $ultima['imc'],
            'gorduraInicial' => $primeira['percentual_gordura'] !== null ? (float) $primeira['percentual_gordura'] : null,
            'gorduraAtual' => $ultima['percentual_gordura'] !== null ? (float) $ultima['percentual_gordura'] : null,
        ];
    }
}
