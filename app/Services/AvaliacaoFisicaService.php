<?php

namespace App\Services;

use App\Repositories\AvaliacaoFisicaRepository;

class AvaliacaoFisicaService
{
    private AvaliacaoFisicaRepository $avaliacaoRepository;

    public function __construct(AvaliacaoFisicaRepository $avaliacaoRepository)
    {
        $this->avaliacaoRepository = $avaliacaoRepository;
    }

    public function createAvaliacao(float $peso, float $altura, ?float $percentualGordura, int $usuarioId): ?int
    {
        // Calcular IMC: peso (kg) / (altura (m))^2
        $alturaMetros = $altura / 100;
        $imc = $peso / ($alturaMetros * $alturaMetros);
        $imc = round($imc, 2);

        $dataAvaliacao = date('Y-m-d');
        return $this->avaliacaoRepository->create($peso, $altura, $imc, $percentualGordura, $dataAvaliacao, $usuarioId);
    }

    public function updateAvaliacao(int $id, float $peso, float $altura, ?float $percentualGordura, string $dataAvaliacao): bool
    {
        // Calcular IMC
        $alturaMetros = $altura / 100;
        $imc = $peso / ($alturaMetros * $alturaMetros);
        $imc = round($imc, 2);

        return $this->avaliacaoRepository->update($id, $peso, $altura, $imc, $percentualGordura, $dataAvaliacao);
    }

    public function getAvaliacaoById(int $id): ?array
    {
        return $this->avaliacaoRepository->find($id);
    }

    public function getAvaliacoesByUsuarioId(int $usuarioId): array
    {
        return $this->avaliacaoRepository->findByUsuarioId($usuarioId);
    }

    public function getLatestAvaliacaoByUsuarioId(int $usuarioId): ?array
    {
        return $this->avaliacaoRepository->findLatestByUsuarioId($usuarioId);
    }

    public function deleteAvaliacao(int $id): bool
    {
        return $this->avaliacaoRepository->delete($id);
    }

    public function getAllAvaliacoes(): array
    {
        return $this->avaliacaoRepository->all();
    }

    public function calculateProgress(int $usuarioId): array
    {
        $avaliacoes = $this->getAvaliacoesByUsuarioId($usuarioId);

        if (count($avaliacoes) < 2) {
            return [
                'pesoInicial' => null,
                'pesoAtual' => null,
                'variacao' => 0,
                'percentualVariacao' => 0,
                'imcInicial' => null,
                'imcAtual' => null,
                'gorduraInicial' => null,
                'gorduraAtual' => null,
            ];
        }

        // Ordenar por data (mais antiga primeiro)
        usort($avaliacoes, function ($a, $b) {
            return strtotime($a['data_avaliacao']) - strtotime($b['data_avaliacao']);
        });

        $primeiraAvaliacao = $avaliacoes[0];
        $ultimaAvaliacao = $avaliacoes[count($avaliacoes) - 1];

        $pesoInicial = (float)$primeiraAvaliacao['peso'];
        $pesoAtual = (float)$ultimaAvaliacao['peso'];
        $variacao = $pesoAtual - $pesoInicial;
        $percentualVariacao = ($variacao / $pesoInicial) * 100;

        return [
            'pesoInicial' => $pesoInicial,
            'pesoAtual' => $pesoAtual,
            'variacao' => round($variacao, 2),
            'percentualVariacao' => round($percentualVariacao, 2),
            'imcInicial' => (float)$primeiraAvaliacao['imc'],
            'imcAtual' => (float)$ultimaAvaliacao['imc'],
            'gorduraInicial' => $primeiraAvaliacao['percentual_gordura'] ? (float)$primeiraAvaliacao['percentual_gordura'] : null,
            'gorduraAtual' => $ultimaAvaliacao['percentual_gordura'] ? (float)$ultimaAvaliacao['percentual_gordura'] : null,
            'totalAvaliacoes' => count($avaliacoes),
        ];
    }
}
