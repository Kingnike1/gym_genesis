<style>
    .plans-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .plans-table th, .plans-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    .plans-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .actions a {
        margin-right: 10px;
        text-decoration: none;
        color: #007bff;
    }
    .actions a:hover {
        text-decoration: underline;
    }
    .add-button {
        display: inline-block;
        background-color: #28a745;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        margin-bottom: 20px;
    }
    .add-button:hover {
        background-color: #218838;
    }
</style>

<div class="plans-container">
    <a href="/gym_genesis/admin/plans/create" class="add-button">Adicionar Novo Plano</a>
    
    <table class="plans-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Preço</th>
                <th>Duração (dias)</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($planos) > 0): ?>
                <?php foreach ($planos as $plano): ?>
                    <tr>
                        <td><?= htmlspecialchars($plano["idplano"]) ?></td>
                        <td><?= htmlspecialchars($plano["tipo"]) ?></td>
                        <td>R$ <?= number_format($plano["preco"], 2, ",", ".") ?></td>
                        <td><?= htmlspecialchars($plano["duriasDias"]) ?></td>
                        <td><?= htmlspecialchars(substr($plano["descricao"], 0, 50)) ?><?= strlen($plano["descricao"]) > 50 ? '...' : '' ?></td>
                        <td class="actions">
                            <a href="/gym_genesis/admin/plans/edit/<?= $plano["idplano"] ?>">Editar</a>
                            <a href="/gym_genesis/admin/plans/delete/<?= $plano["idplano"] ?>" onclick="return confirm('Tem certeza que deseja deletar este plano?');">Deletar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum plano encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
