<style>
    .orders-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .orders-table th, .orders-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    .orders-table th { background-color: #f2f2f2; font-weight: bold; }
    .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 0.85em; font-weight: bold; }
    .status-pendente { background-color: #ffeeba; color: #856404; }
    .status-processando { background-color: #b8daff; color: #004085; }
    .status-enviado { background-color: #c3e6cb; color: #155724; }
    .status-entregue { background-color: #d4edda; color: #155724; }
    .status-cancelado { background-color: #f5c6cb; color: #721c24; }
</style>

<div class="orders-container">
    <table class="orders-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário ID</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th>Data do Pedido</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($pedidos) && count($pedidos) > 0): ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= htmlspecialchars($pedido["idpedido"]) ?></td>
                        <td><?= htmlspecialchars($pedido["usuario_id"]) ?></td>
                        <td>R$ <?= number_format($pedido["valor_total"], 2, ",", ".") ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower(htmlspecialchars($pedido["status"])) ?>">
                                <?= htmlspecialchars(ucfirst($pedido["status"])) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars(date("d/m/Y H:i", strtotime($pedido["data_pedido"]))) ?></td>
                        <td class="actions">
                            <a href="/gym_genesis/admin/orders/<?= $pedido["idpedido"] ?>">Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum pedido encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
