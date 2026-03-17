<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Admin</title>
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
        .form-group input[type="email"],
        .form-group input[type="password"],
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Usuário</h1>
        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <form action="/gym_genesis/admin/users/<?= htmlspecialchars($user["idusuario"]) ?>" method="POST">
            <div class="form-group">
                <label for="idusuario">ID do Usuário:</label>
                <input type="text" id="idusuario" name="idusuario" value="<?= htmlspecialchars($user["idusuario"]) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Nova Senha (deixe em branco para não alterar):</label>
                <input type="password" id="password" name="password" placeholder="********">
            </div>
            <div class="form-group">
                <label for="user_type">Tipo de Usuário:</label>
                <select id="user_type" name="user_type" required>
                    <option value="1" <?= ($user["tipo_usuario"] == 1) ? "selected" : "" ?>>Administrador</option>
                    <option value="2" <?= ($user["tipo_usuario"] == 2) ? "selected" : "" ?>>Professor</option>
                    <option value="3" <?= ($user["tipo_usuario"] == 3) ? "selected" : "" ?>>Aluno</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Atualizar Usuário</button>
            </div>
        </form>
    </div>
</body>
</html>
