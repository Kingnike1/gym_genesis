<h2>Minhas Avaliações Físicas</h2>
<p>Histórico de todas as suas avaliações físicas.</p>

<?php if (isset($errorMessage)): ?>
    <div style="background-color: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<?php if (isset($successMessage)): ?>
    <div style="background-color: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= htmlspecialchars($successMessage) ?>
    </div>
<?php endif; ?>

<div style="margin-bottom: 20px;">
    <a href="/student/avaliacoes/create" style="display: inline-block; background-color: #1976d2; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
        + Nova Avaliação
    </a>
</div>

<?php if (count($avaliacoes) > 0): ?>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                <th style="padding: 12px; text-align: left;">Data</th>
                <th style="padding: 12px; text-align: left;">Peso (kg)</th>
                <th style="padding: 12px; text-align: left;">Altura (cm)</th>
                <th style="padding: 12px; text-align: left;">IMC</th>
                <th style="padding: 12px; text-align: left;">Gordura (%)</th>
                <th style="padding: 12px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($avaliacoes as $avaliacao): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px;"><?= htmlspecialchars(date('d/m/Y', strtotime($avaliacao['data_avaliacao']))) ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars(number_format($avaliacao['peso'], 2, ',', '.')) ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars(number_format($avaliacao['altura'], 2, ',', '.')) ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars(number_format($avaliacao['imc'], 2, ',', '.')) ?></td>
                    <td style="padding: 12px;">
                        <?= $avaliacao['percentual_gordura'] ? htmlspecialchars(number_format($avaliacao['percentual_gordura'], 2, ',', '.')) : '-' ?>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="/student/avaliacoes/<?= htmlspecialchars($avaliacao['idavaliacao']) ?>" style="color: #1976d2; text-decoration: none; margin-right: 10px;">Ver</a>
                        <a href="/student/avaliacoes/<?= htmlspecialchars($avaliacao['idavaliacao']) ?>/edit" style="color: #f57c00; text-decoration: none; margin-right: 10px;">Editar</a>
                        <a href="/student/avaliacoes/<?= htmlspecialchars($avaliacao['idavaliacao']) ?>/delete" style="color: #d32f2f; text-decoration: none;" onclick="return confirm('Tem certeza que deseja deletar esta avaliação?');">Deletar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div style="background-color: #fff9c4; padding: 20px; border-radius: 8px; margin-top: 20px;">
        <p>Você ainda não tem avaliações registradas. <a href="/student/avaliacoes/create">Crie sua primeira avaliação</a> para começar a acompanhar seu progresso!</p>
    </div>
<?php endif; ?>

<div style="margin-top: 30px;">
    <h3>Ações Rápidas</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 10px;">
            <a href="/student/progresso" style="display: inline-block; background-color: #388e3c; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
                Ver Progresso Geral
            </a>
        </li>
        <li style="margin-bottom: 10px;">
            <a href="/student/dashboard" style="display: inline-block; background-color: #7b1fa2; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
                Voltar ao Dashboard
            </a>
        </li>
    </ul>
</div>
