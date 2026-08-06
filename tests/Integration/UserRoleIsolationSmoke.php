<?php

require_once __DIR__ . '/../../bootstrap/app.php';

use App\Security\SessionManager;
use App\Services\Database;
use App\Tenancy\AcademyContext;

$pdo = Database::getConnection();
$pdo->beginTransaction();

try {
    $pdo->exec("INSERT INTO academias (nome, status) VALUES ('Role Test A', 'ativa')");
    $academyA = (int) $pdo->lastInsertId();
    $pdo->exec("INSERT INTO academias (nome, status) VALUES ('Role Test B', 'ativa')");
    $academyB = (int) $pdo->lastInsertId();

    $password = password_hash('role-test', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuario (senha, email, tipo_usuario, status) VALUES (?, ?, 1, 'ativo')");
    $stmt->execute([$password, 'role-' . bin2hex(random_bytes(4)) . '@example.test']);
    $userId = (int) $pdo->lastInsertId();

    $link = $pdo->prepare('INSERT INTO academia_usuario (academia_id, usuario_id, papel, is_principal, ativo) VALUES (?, ?, ?, ?, 1)');
    $link->execute([$academyA, $userId, 1, 1]);
    $link->execute([$academyB, $userId, 3, 0]);

    SessionManager::start();
    $_SESSION['user_id'] = $userId;

    AcademyContext::clear();
    AcademyContext::select($academyA);
    if (AcademyContext::role() !== 1 || (int) $_SESSION['user_type'] !== 1) {
        throw new RuntimeException('Falha: papel da Academia A não foi aplicado.');
    }

    AcademyContext::select($academyB);
    if (AcademyContext::role() !== 3 || (int) $_SESSION['user_type'] !== 3) {
        throw new RuntimeException('Falha: papel da Academia B não foi aplicado.');
    }

    echo "OK: papéis por academia validados.\n";
} finally {
    $pdo->rollBack();
    AcademyContext::clear();
}
