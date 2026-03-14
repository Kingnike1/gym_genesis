<h2>Bem-vindo ao seu Dashboard</h2>
<p>Aqui você pode acompanhar seus treinos, dietas e progresso na academia.</p>

<!-- Exemplo de cards de métricas -->
<div style="display: flex; gap: 20px; margin-top: 20px;">
    <div style="background-color: #e8f5e9; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Meus Treinos</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars(count($meusTreinos)) ?></p>
    </div>
    <div style="background-color: #e0f7fa; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Minhas Dietas</h3>
        <p style="font-size: 2em;"><?= htmlspecialchars(count($minhasDietas)) ?></p>
    </div>
</div>

<h2 style="margin-top: 40px;">Últimos Treinos</h2>
<?php if (count($meusTreinos) > 0): ?>
    <ul>
        <?php foreach (array_slice($meusTreinos, 0, 5) as $treino): ?>
            <li><?= htmlspecialchars($treino["nome"]) ?> - <?= htmlspecialchars($treino["data_criacao"]) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Você ainda não tem treinos atribuídos.</p>
<?php endif; ?>

<h2>Últimas Dietas</h2>
<?php if (count($minhasDietas) > 0): ?>
    <ul>
        <?php foreach (array_slice($minhasDietas, 0, 5) as $dieta): ?>
            <li><?= htmlspecialchars($dieta["nome"]) ?> - <?= htmlspecialchars($dieta["data_criacao"]) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Você ainda não tem dietas atribuídas.</p>
<?php endif; ?>

<h2 style="margin-top: 40px;">Ações Rápidas</h2>
<ul>
    <li><a href="/gym_genesis/student/treinos">Ver Todos os Treinos</a></li>
    <li><a href="/gym_genesis/student/dietas">Ver Todas as Dietas</a></li>
    <li><a href="/gym_genesis/student/perfil">Editar Perfil</a></li>
</ul>
