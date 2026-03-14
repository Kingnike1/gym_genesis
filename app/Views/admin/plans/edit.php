<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Plano - Admin</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Plano</h1>
        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <form action="/gym_genesis/admin/plans/<?= htmlspecialchars($plano["idplano"]) ?>" method="POST">
            <div class="form-group">
                <label for="idplano">ID do Plano:</label>
                <input type="text" id="idplano" name="idplano" value="<?= htmlspecialchars($plano["idplano"]) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Plano:</label>
                <input type="text" id="tipo" name="tipo" value="<?= htmlspecialchars($plano["tipo"]) ?>" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço (R$):</label>
                <input type="number" id="preco" name="preco" step="0.01" value="<?= htmlspecialchars($plano["preco"]) ?>" required>
            </div>
            <div class="form-group">
                <label for="duriasDias">Duração (dias):</label>
                <input type="number" id="duriasDias" name="duriasDias" value="<?= htmlspecialchars($plano["duriasDias"]) ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($plano["descricao"]) ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Atualizar Plano</button>
            </div>
        </form>
    </div>
</body>
</html>
