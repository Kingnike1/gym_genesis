<style>
    .products-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .products-table th, .products-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    .products-table th { background-color: #f2f2f2; font-weight: bold; }
    .actions a { margin-right: 10px; text-decoration: none; color: #007bff; }
    .add-button { display: inline-block; background-color: #28a745; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin-bottom: 20px; }
</style>

<div class="products-container">
    <a href="/gym_genesis/admin/products/create" class="add-button">Adicionar Novo Produto</a>
    
    <table class="products-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($produtos) && count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto["idproduto"]) ?></td>
                        <td><?= htmlspecialchars($produto["nome"]) ?></td>
                        <td><?= htmlspecialchars($produto["categoria"]) ?></td>
                        <td>R$ <?= number_format($produto["preco"], 2, ",", ".") ?></td>
                        <td><?= htmlspecialchars($produto["estoque"]) ?></td>
                        <td class="actions">
                            <a href="/gym_genesis/admin/products/edit/<?= $produto["idproduto"] ?>">Editar</a>
                            <a href="/gym_genesis/admin/products/delete/<?= $produto["idproduto"] ?>" onclick="return confirm(\'Tem certeza que deseja deletar este produto?\');">Deletar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum produto encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
