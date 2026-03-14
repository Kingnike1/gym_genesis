<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Treino - Aluno</title>
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
        .back-link {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .back-link:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/gym_genesis/student/treinos" class="back-link">Voltar para Meus Treinos</a>
        <h1>Detalhes do Treino</h1>
        
        <div class="details">
            <p><strong>ID do Treino:</strong> <?= htmlspecialchars($treino["idtreino"]) ?></p>
            <p><strong>Nome:</strong> <?= htmlspecialchars($treino["nome"]) ?></p>
            <p><strong>Descrição:</strong> <?= htmlspecialchars($treino["descricao"]) ?></p>
            <p><strong>Data de Criação:</strong> <?= htmlspecialchars($treino["data_criacao"]) ?></p>
        </div>
    </div>
</body>
</html>
