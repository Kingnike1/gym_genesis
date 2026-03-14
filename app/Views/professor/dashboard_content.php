<h2>Visão Geral</h2>
<p>Aqui você verá um resumo das atividades com seus alunos, treinos e dietas.</p>

<!-- Exemplo de cards de métricas -->
<div style="display: flex; gap: 20px; margin-top: 20px;">
    <div style="background-color: #e8f5e9; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Alunos</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars($totalAlunos) ?></p>
    </div>
    <div style="background-color: #e0f7fa; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Treinos Criados</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars($totalTreinos) ?></p>
    </div>
    <div style="background-color: #fff3e0; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Dietas Criadas</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars($totalDietas) ?></p>
    </div>
</div>

<h2 style="margin-top: 40px;">Ações Rápidas</h2>
<ul>
    <li><a href="/gym_genesis/professor/treinos/create">Criar Novo Treino</a></li>
    <li><a href="/gym_genesis/professor/dietas/create">Criar Nova Dieta</a></li>
    <li><a href="/gym_genesis/professor/treinos">Ver Todos os Treinos</a></li>
    <li><a href="/gym_genesis/professor/dietas">Ver Todas as Dietas</a></li>
</ul>
