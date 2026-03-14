<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - Admin</title>
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
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
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
        <h1>Gerenciar Pedidos</h1>
        <table>
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
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= htmlspecialchars($pedido["idpedido"]) ?></td>
                        <td><?= htmlspecialchars($pedido["usuario_id"]) ?></td>
                        <td>R$ <?= number_format($pedido["valor_total"], 2, ",", ".") ?></td>
                        <td><span class="status <?= htmlspecialchars($pedido["status"]) ?>"><?= htmlspecialchars(ucfirst($pedido["status"])) ?></span></td>
                        <td><?= htmlspecialchars($pedido["data_pedido"]) ?></td>
                        <td class="actions">
                            <a href="/gym_genesis/admin/orders/<?= $pedido["idpedido"] ?>">Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
