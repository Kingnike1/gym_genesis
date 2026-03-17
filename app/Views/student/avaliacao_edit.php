<h2>Editar Avaliação Física</h2>
<p>Atualize suas medidas.</p>

<?php if (isset($errorMessage)): ?>
    <div style="background-color: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/student/avaliacoes/<?= htmlspecialchars($avaliacao['idavaliacao']) ?>" style="max-width: 500px;">
    <div style="margin-bottom: 20px;">
        <label for="peso" style="display: block; margin-bottom: 5px; font-weight: bold;">Peso (kg) *</label>
        <input type="number" id="peso" name="peso" step="0.01" min="0" required value="<?= htmlspecialchars($avaliacao['peso']) ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="altura" style="display: block; margin-bottom: 5px; font-weight: bold;">Altura (cm) *</label>
        <input type="number" id="altura" name="altura" step="0.01" min="0" required value="<?= htmlspecialchars($avaliacao['altura']) ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="percentual_gordura" style="display: block; margin-bottom: 5px; font-weight: bold;">Percentual de Gordura (%) <span style="color: #999;">(opcional)</span></label>
        <input type="number" id="percentual_gordura" name="percentual_gordura" step="0.01" min="0" max="100" value="<?= htmlspecialchars($avaliacao['percentual_gordura'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="data_avaliacao" style="display: block; margin-bottom: 5px; font-weight: bold;">Data da Avaliação *</label>
        <input type="date" id="data_avaliacao" name="data_avaliacao" required value="<?= htmlspecialchars($avaliacao['data_avaliacao']) ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="display: flex; gap: 10px;">
        <button type="submit" style="background-color: #f57c00; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Atualizar Avaliação
        </button>
        <a href="/student/avaliacoes" style="display: inline-block; background-color: #999; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
            Cancelar
        </a>
    </div>
</form>

<div style="margin-top: 30px; background-color: #f5f5f5; padding: 20px; border-radius: 8px;">
    <h3>Informações Atuais</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 10px;"><strong>IMC:</strong> <?= htmlspecialchars(number_format($avaliacao['imc'], 2, ',', '.')) ?></li>
        <li style="margin-bottom: 10px;"><strong>Data de Criação:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($avaliacao['data_avaliacao']))) ?></li>
    </ul>
</div>
