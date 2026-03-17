<h2>Meu Progresso</h2>
<p>Acompanhe sua evolução física e desempenho na academia.</p>

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

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <?php if ($progress['totalAvaliacoes'] > 0): ?>
        <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; border-left: 4px solid #1976d2;">
            <h3 style="margin-top: 0;">Peso Atual</h3>
            <p style="font-size: 2em; margin: 10px 0; color: #1976d2;">
                <?= htmlspecialchars(number_format($progress['pesoAtual'], 2, ',', '.')) ?> kg
            </p>
            <?php if ($progress['variacao'] != 0): ?>
                <p style="margin: 0; font-size: 0.9em;">
                    <?php if ($progress['variacao'] > 0): ?>
                        <span style="color: #d32f2f;">↑ +<?= htmlspecialchars(number_format($progress['variacao'], 2, ',', '.')) ?> kg (<?= htmlspecialchars($progress['percentualVariacao']) ?>%)</span>
                    <?php else: ?>
                        <span style="color: #388e3c;">↓ <?= htmlspecialchars(number_format($progress['variacao'], 2, ',', '.')) ?> kg (<?= htmlspecialchars($progress['percentualVariacao']) ?>%)</span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <div style="background-color: #f3e5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #7b1fa2;">
            <h3 style="margin-top: 0;">IMC Atual</h3>
            <p style="font-size: 2em; margin: 10px 0; color: #7b1fa2;">
                <?= htmlspecialchars(number_format($progress['imcAtual'], 2, ',', '.')) ?>
            </p>
            <p style="margin: 0; font-size: 0.85em;">
                <?php
                $imc = $progress['imcAtual'];
                if ($imc < 18.5) {
                    $categoria = "Abaixo do peso";
                    $cor = "#1976d2";
                } elseif ($imc < 25) {
                    $categoria = "Peso normal";
                    $cor = "#388e3c";
                } elseif ($imc < 30) {
                    $categoria = "Sobrepeso";
                    $cor = "#f57c00";
                } else {
                    $categoria = "Obeso";
                    $cor = "#d32f2f";
                }
                ?>
                <span style="color: <?= $cor ?>;"><?= htmlspecialchars($categoria) ?></span>
            </p>
        </div>

        <?php if ($progress['gorduraAtual'] !== null): ?>
            <div style="background-color: #fff3e0; padding: 20px; border-radius: 8px; border-left: 4px solid #e65100;">
                <h3 style="margin-top: 0;">Percentual de Gordura</h3>
                <p style="font-size: 2em; margin: 10px 0; color: #e65100;">
                    <?= htmlspecialchars(number_format($progress['gorduraAtual'], 2, ',', '.')) ?>%
                </p>
                <?php if ($progress['gorduraInicial'] !== null): ?>
                    <p style="margin: 0; font-size: 0.9em;">
                        Inicial: <?= htmlspecialchars(number_format($progress['gorduraInicial'], 2, ',', '.')) ?>%
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="background-color: #e0f2f1; padding: 20px; border-radius: 8px; border-left: 4px solid #00796b;">
            <h3 style="margin-top: 0;">Total de Avaliações</h3>
            <p style="font-size: 2em; margin: 10px 0; color: #00796b;">
                <?= htmlspecialchars($progress['totalAvaliacoes']) ?>
            </p>
            <p style="margin: 0; font-size: 0.9em;">avaliações registradas</p>
        </div>
    <?php else: ?>
        <div style="background-color: #fff9c4; padding: 20px; border-radius: 8px; grid-column: 1 / -1;">
            <p>Você ainda não tem avaliações registradas. <a href="/student/avaliacoes/create">Crie sua primeira avaliação</a> para começar a acompanhar seu progresso!</p>
        </div>
    <?php endif; ?>
</div>

<div style="margin-top: 30px;">
    <h3>Ações Rápidas</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 10px;">
            <a href="/student/avaliacoes/create" style="display: inline-block; background-color: #1976d2; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
                + Nova Avaliação
            </a>
        </li>
        <li style="margin-bottom: 10px;">
            <a href="/student/avaliacoes" style="display: inline-block; background-color: #388e3c; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
                Ver Todas as Avaliações
            </a>
        </li>
    </ul>
</div>
