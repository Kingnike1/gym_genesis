<h2>Meu Perfil</h2>
<p>Visualize e edite suas informações pessoais.</p>

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

<form method="POST" action="/student/perfil" style="max-width: 600px;">
    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3 style="margin-top: 0;">Informações Pessoais</h3>

        <div style="margin-bottom: 20px;">
            <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email *</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="tipo_usuario" style="display: block; margin-bottom: 5px; font-weight: bold;">Tipo de Usuário</label>
            <input type="text" id="tipo_usuario" disabled value="Aluno" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; background-color: #e8e8e8;">
        </div>
    </div>

    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3 style="margin-top: 0;">Alterar Senha</h3>
        <p style="color: #666; font-size: 0.9em;">Deixe em branco se não deseja alterar sua senha.</p>

        <div style="margin-bottom: 20px;">
            <label for="senha_atual" style="display: block; margin-bottom: 5px; font-weight: bold;">Senha Atual</label>
            <input type="password" id="senha_atual" name="senha_atual" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="nova_senha" style="display: block; margin-bottom: 5px; font-weight: bold;">Nova Senha</label>
            <input type="password" id="nova_senha" name="nova_senha" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="confirmar_senha" style="display: block; margin-bottom: 5px; font-weight: bold;">Confirmar Nova Senha</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
        </div>
    </div>

    <div style="display: flex; gap: 10px;">
        <button type="submit" style="background-color: #1976d2; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Salvar Alterações
        </button>
        <a href="/student/dashboard" style="display: inline-block; background-color: #999; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">
            Cancelar
        </a>
    </div>
</form>

<div style="margin-top: 30px; background-color: #fff3e0; padding: 20px; border-radius: 8px;">
    <h3 style="margin-top: 0;">Dicas de Segurança</h3>
    <ul>
        <li>Use uma senha forte com letras maiúsculas, minúsculas, números e símbolos.</li>
        <li>Não compartilhe sua senha com ninguém.</li>
        <li>Altere sua senha regularmente (a cada 3 meses).</li>
        <li>Se suspeitar de acesso não autorizado, altere sua senha imediatamente.</li>
    </ul>
</div>
