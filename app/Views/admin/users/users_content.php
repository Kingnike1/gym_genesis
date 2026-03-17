<style>
    .users-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .users-table th, .users-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    .users-table th { background-color: #f2f2f2; font-weight: bold; }
    .actions a { margin-right: 10px; text-decoration: none; color: #007bff; }
    .add-button { display: inline-block; background-color: #28a745; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin-bottom: 20px; }
</style>

<div class="users-container">
    <a href="/gym_genesis/admin/users/create" class="add-button">Adicionar Novo Usuário</a>
    
    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user["idusuario"]) ?></td>
                    <td><?= htmlspecialchars($user["email"]) ?></td>
                    <td>
                        <?php 
                            switch($user["tipo_usuario"]) {
                                case 1: echo "Administrador"; break;
                                case 2: echo "Professor"; break;
                                case 3: echo "Aluno"; break;
                                default: echo htmlspecialchars($user["tipo_usuario"]);
                            }
                        ?>
                    </td>
                    <td class="actions">
                        <a href="/gym_genesis/admin/users/edit/<?= $user["idusuario"] ?>">Editar</a>
                        <a href="/gym_genesis/admin/users/delete/<?= $user["idusuario"] ?>" onclick="return confirm('Tem certeza que deseja deletar este usuário?');">Deletar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
