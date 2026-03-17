<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Aluno' ?> - Gym Genesis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 200px;
            background-color: #1a5f7a;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 8px 10px;
            border-radius: 4px;
        }
        .sidebar ul li a:hover {
            background-color: #2a7fa3;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .header {
            background-color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header .logout a {
            color: #dc3545;
            text-decoration: none;
        }
        .header .logout a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Gym Genesis</h2>
        <ul>
            <li><a href="/gym_genesis/student/dashboard">Dashboard</a></li>
            <li><a href="/gym_genesis/student/treinos">Meus Treinos</a></li>
            <li><a href="/gym_genesis/student/dietas">Minhas Dietas</a></li>
            <li><a href="/gym_genesis/student/progresso">Progresso</a></li>
            <li><a href="/gym_genesis/student/perfil">Perfil</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="header">
            <h1><?= $title ?? 'Dashboard' ?></h1>
            <div class="logout">
                <a href="/gym_genesis/logout">Sair</a>
            </div>
        </div>
        <div class="main-content">
            <?php include $contentView; // Variável que conterá o caminho para a view específica da página ?>
        </div>
    </div>
</body>
</html>
