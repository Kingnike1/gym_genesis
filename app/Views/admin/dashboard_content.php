<h2>Visão Geral</h2>
<p>Aqui você verá um resumo das atividades da academia, como número de alunos, professores, planos ativos, etc.</p>

<!-- Exemplo de cards de métricas -->
<div style="display: flex; gap: 20px; margin-top: 20px;">
    <div style="background-color: #e0f7fa; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Total de Usuários</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars($totalUsers) ?></p>
    </div>
    <div style="background-color: #e8f5e9; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Total de Professores</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars($totalProfessors) ?></p>
    </div>
    <div style="background-color: #fff3e0; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Planos Ativos</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars($activePlans) ?></p>
    </div>
</div>

<h2 style="margin-top: 40px;">Atividades Recentes</h2>
<ul>
    <li>Novo aluno cadastrado: João Silva</li>
    <li>Plano renovado: Maria Souza (Plano Premium)</li>
    <li>Novo produto adicionado à loja: Suplemento X</li>
</ul>
