<h2>Detalhes da Avaliação</h2>

<div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h3 style="margin-top: 0;">Data: <?= htmlspecialchars(date('d/m/Y', strtotime($avaliacao['data_avaliacao']))) ?></h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        <div style="background-color: white; padding: 15px; border-radius: 4px; border-left: 4px solid #1976d2;">
            <p style="margin: 0; color: #999; font-size: 0.9em;">Peso</p>
            <p style="margin: 10px 0 0 0; font-size: 1.8em; color: #1976d2; font-weight: bold;">
                <?= htmlspecialchars(number_format($avaliacao['peso'], 2, ',', '.')) ?> kg
            </p>
        </div>

        <div style="background-color: white; padding: 15px; border-radius: 4px; border-left: 4px solid #388e3c;">
            <p style="margin: 0; color: #999; font-size: 0.9em;">Altura</p>
            <p style="margin: 10px 0 0 0; font-size: 1.8em; color: #388e3c; font-weight: bold;">
                <?= htmlspecialchars(number_format($avaliacao['altura'], 2, ',', '.')) ?> cm
            </p>
        </div>

        <div style="background-color: white; padding: 15px; border-radius: 4px; border-left: 4px solid #7b1fa2;">
            <p style="margin: 0; color: #999; font-size: 0.9em;">IMC</p>
            <p style="margin: 10px 0 0 0; font-size: 1.8em; color: #7b1fa2; font-weight: bold;">
                <?= htmlspecialchars(number_format($avaliacao['imc'], 2, ',', '.')) ?>
            </p>
            <?php
            $imc = $avaliacao['imc'];
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
            <p style="margin: 5px 0 0 0; font-size: 0.85em; color: <?= $cor ?>;">
                <?= htmlspecialchars($categoria) ?>
            </p>
        </div>

        <?php if ($avaliacao['percentual_gordura']): ?>
            <div style="background-color: white; padding: 15px; border-radius: 4px; border-left: 4px solid #e65100;">
                <p style="margin: 0; color: #999; font-size: 0.9em;">Percentual de Gordura</p>
                <p style="margin: 10px 0 0 0; font-size: 1.8em; color: #e65100; font-weight: bold;">
                    <?= htmlspecialchars(number_format($avaliacao['percentual_gordura'], 2, ',', '.')) ?>%
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div style="display: flex; gap: 10px; margin-bottom: 20px;">
    <a href="/student/avaliacoes/<?= htmlspecialchars($avaliacao['idavaliacao']) ?>/edit" style="display: inline-block; background-color: #f57c00; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
        Editar
    </a>
    <a href="/student/avaliacoes/<?= htmlspecialchars($avaliacao['idavaliacao']) ?>/delete" style="display: inline-block; background-color: #d32f2f; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;" onclick="return confirm('Tem certeza que deseja deletar esta avaliação?');">
        Deletar
    </a>
    <a href="/student/avaliacoes" style="display: inline-block; background-color: #999; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
        Voltar
    </a>
</div>

<div style="background-color: #e8f5e9; padding: 15px; border-radius: 8px;">
    <h3 style="margin-top: 0;">Dicas para Melhorar</h3>
    <ul>
        <li>Mantenha uma alimentação equilibrada e balanceada.</li>
        <li>Pratique atividades físicas regularmente (pelo menos 30 minutos por dia).</li>
        <li>Durma bem (7-8 horas por noite).</li>
        <li>Beba bastante água ao longo do dia.</li>
        <li>Consulte um nutricionista para um plano personalizado.</li>
    </ul>
</div>
