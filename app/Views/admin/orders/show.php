<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 20px;
        }
        .details p {
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
        .status.pendente {
            background-color: #fff3cd;
            color: #856404;
        }
        .status.processando {
            background-color: #cfe2ff;
            color: #084298;
        }
        .status.enviado {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status.entregue {
            background-color: #d4edda;
            color: #155724;
        }
        .status.cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detalhes do Pedido #<?= htmlspecialchars($pedido["idpedido"]) ?></h1>
        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        
        <div class="details">
            <p><strong>ID do Pedido:</strong> <?= htmlspecialchars($pedido["idpedido"]) ?></p>
            <p><strong>ID do Usuário:</strong> <?= htmlspecialchars($pedido["usuario_id"]) ?></p>
            <p><strong>Valor Total:</strong> R$ <?= number_format($pedido["valor_total"], 2, ",", ".") ?></p>
            <p><strong>Status Atual:</strong> <span class="status <?= htmlspecialchars($pedido["status"]) ?>"><?= htmlspecialchars(ucfirst($pedido["status"])) ?></span></p>
            <p><strong>Data do Pedido:</strong> <?= htmlspecialchars($pedido["data_pedido"]) ?></p>
        </div>

        <h2>Atualizar Status</h2>
        <form action="/gym_genesis/admin/orders/<?= htmlspecialchars($pedido["idpedido"]) ?>/status" method="POST">
            <div class="form-group">
                <label for="status">Novo Status:</label>
                <select id="status" name="status" required>
                    <option value="pendente" <?= ($pedido["status"] == "pendente") ? "selected" : "" ?>>Pendente</option>
                    <option value="processando" <?= ($pedido["status"] == "processando") ? "selected" : "" ?>>Processando</option>
                    <option value="enviado" <?= ($pedido["status"] == "enviado") ? "selected" : "" ?>>Enviado</option>
                    <option value="entregue" <?= ($pedido["status"] == "entregue") ? "selected" : "" ?>>Entregue</option>
                    <option value="cancelado" <?= ($pedido["status"] == "cancelado") ? "selected" : "" ?>>Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Atualizar Status</button>
            </div>
        </form>
    </div>
</body>
</html>
