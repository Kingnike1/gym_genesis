<style>
    .order-details { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .order-info { margin-bottom: 20px; }
    .order-info p { margin: 10px 0; padding: 10px; background-color: #f9f9f9; border-left: 4px solid #007bff; }
    .status-badge { padding: 5px 10px; border-radius: 4px; font-weight: bold; display: inline-block; }
    .status-pendente { background-color: #fff3cd; color: #856404; }
    .status-processando { background-color: #cfe2ff; color: #084298; }
    .status-enviado { background-color: #d1ecf1; color: #0c5460; }
    .status-entregue { background-color: #d4edda; color: #155724; }
    .status-cancelado { background-color: #f8d7da; color: #721c24; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .status-select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    .btn-update { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
    .btn-back { background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 4px; text-decoration: none; display: inline-block; margin-top: 20px; }
    .error { color: red; margin-bottom: 15px; }
</style>

<div class="order-details">
    <?php if (isset($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <div class="order-info">
        <p><strong>ID do Pedido:</strong> <?= htmlspecialchars($pedido["idpedido"]) ?></p>
        <p><strong>ID do Usuário:</strong> <?= htmlspecialchars($pedido["usuario_id"]) ?></p>
        <p><strong>Valor Total:</strong> R$ <?= number_format($pedido["valor_total"], 2, ",", ".") ?></p>
        <p><strong>Status Atual:</strong> <span class="status-badge status-<?= htmlspecialchars($pedido["status"]) ?>"><?= htmlspecialchars(ucfirst($pedido["status"])) ?></span></p>
        <p><strong>Data do Pedido:</strong> <?= htmlspecialchars(date("d/m/Y H:i", strtotime($pedido["data_pedido"]))) ?></p>
    </div>

    <div class="status-form">
        <h3>Atualizar Status do Pedido</h3>
        <form action="/gym_genesis/admin/orders/<?= htmlspecialchars($pedido["idpedido"]) ?>/status" method="POST">
            <div class="form-group">
                <label for="status">Novo Status:</label>
                <select id="status" name="status" class="status-select" required>
                    <option value="pendente" <?= ($pedido["status"] == "pendente") ? "selected" : "" ?>>Pendente</option>
                    <option value="processando" <?= ($pedido["status"] == "processando") ? "selected" : "" ?>>Processando</option>
                    <option value="enviado" <?= ($pedido["status"] == "enviado") ? "selected" : "" ?>>Enviado</option>
                    <option value="entregue" <?= ($pedido["status"] == "entregue") ? "selected" : "" ?>>Entregue</option>
                    <option value="cancelado" <?= ($pedido["status"] == "cancelado") ? "selected" : "" ?>>Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-update">Atualizar Status</button>
            </div>
        </form>
    </div>

    <a href="/gym_genesis/admin/orders" class="btn-back">Voltar para Pedidos</a>
</div>
