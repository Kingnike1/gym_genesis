<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Treinos - Aluno</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Meus Treinos</h1>
        <?php if (count($treinos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($treinos as $treino): ?>
                        <tr>
                            <td><?= htmlspecialchars($treino["idtreino"]) ?></td>
                            <td><?= htmlspecialchars($treino["nome"]) ?></td>
                            <td><?= htmlspecialchars($treino["data_criacao"]) ?></td>
                            <td class="actions">
                                <a href="/gym_genesis/student/treinos/<?= $treino["idtreino"] ?>">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Você ainda não tem treinos atribuídos.</p>
        <?php endif; ?>
    </div>
</body>
</html>
