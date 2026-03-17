<h2>Nova Avaliação Física</h2>
<p>Registre suas medidas para acompanhar seu progresso.</p>

<?php if (isset($errorMessage)): ?>
    <div style="background-color: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/student/avaliacoes" style="max-width: 500px;">
    <div style="margin-bottom: 20px;">
        <label for="peso" style="display: block; margin-bottom: 5px; font-weight: bold;">Peso (kg) *</label>
        <input type="number" id="peso" name="peso" step="0.01" min="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="altura" style="display: block; margin-bottom: 5px; font-weight: bold;">Altura (cm) *</label>
        <input type="number" id="altura" name="altura" step="0.01" min="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="percentual_gordura" style="display: block; margin-bottom: 5px; font-weight: bold;">Percentual de Gordura (%) <span style="color: #999;">(opcional)</span></label>
        <input type="number" id="percentual_gordura" name="percentual_gordura" step="0.01" min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="display: flex; gap: 10px;">
        <button type="submit" style="background-color: #1976d2; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Registrar Avaliação
        </button>
        <a href="/student/avaliacoes" style="display: inline-block; background-color: #999; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
            Cancelar
        </a>
    </div>
</form>

<div style="margin-top: 30px; background-color: #f5f5f5; padding: 20px; border-radius: 8px;">
    <h3>Dicas para Medir</h3>
    <ul>
        <li><strong>Peso:</strong> Meça em uma balança confiável, preferencialmente pela manhã, em jejum.</li>
        <li><strong>Altura:</strong> Meça descalço, com a costas reta contra uma parede.</li>
        <li><strong>Percentual de Gordura:</strong> Pode ser medido com um adipômetro ou em uma avaliação profissional.</li>
    </ul>
</div>
