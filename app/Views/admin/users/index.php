<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Admin</title>
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
</head>
<body>
    <div class="container">
        <h1>Gerenciar Usuários</h1>
        <a href="/gym_genesis/admin/users/create" class="add-button">Adicionar Novo Usuário</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>E-mail</th>
                    <th>Tipo de Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user["idusuario"]) ?></td>
                        <td><?= htmlspecialchars($user["email"]) ?></td>
                        <td><?= htmlspecialchars($user["tipo_usuario"]) ?></td>
                        <td class="actions">
                            <a href="/gym_genesis/admin/users/edit/<?= $user["idusuario"] ?>">Editar</a>
                            <a href="/gym_genesis/admin/users/delete/<?= $user["idusuario"] ?>" onclick="return confirm(\'Tem certeza que deseja deletar este usuário?\');">Deletar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
