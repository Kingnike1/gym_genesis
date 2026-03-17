<style>
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    .btn-save { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    .btn-cancel { background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
    .error { color: red; margin-bottom: 10px; }
</style>

<div style="background: white; padding: 20px; border-radius: 8px; max-width: 600px;">
    <?php if (isset($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>
    <form action="/gym_genesis/admin/plans" method="POST">
        <div class="form-group">
            <label for="tipo">Tipo de Plano:</label>
            <input type="text" id="tipo" name="tipo" placeholder="Ex: Premium, Standard, Basic" required>
        </div>
        <div class="form-group">
            <label for="preco">Preço (R$):</label>
            <input type="number" id="preco" name="preco" step="0.01" placeholder="0.00" required>
        </div>
        <div class="form-group">
            <label for="duriasDias">Duração (dias):</label>
            <input type="number" id="duriasDias" name="duriasDias" placeholder="30" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" placeholder="Descreva os benefícios do plano..." required></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn-save">Salvar Plano</button>
            <a href="/gym_genesis/admin/plans" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
