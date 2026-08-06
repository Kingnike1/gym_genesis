<?php

require_once __DIR__ . '/../../bootstrap/app.php';

use App\Repositories\PlanoRepository;
use App\Security\SessionManager;
use App\Services\Database;
use App\Tenancy\AcademyContext;

$pdo = Database::getConnection();
$pdo->beginTransaction();

try {
    $pdo->exec("INSERT INTO academias (nome, status) VALUES ('Academia Teste A', 'ativa'), ('Academia Teste B', 'ativa')");
    $academyB = (int) $pdo->lastInsertId();
    $academyA = $academyB - 1;

    $password = password_hash('tenancy-test', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO usuario (senha, email, tipo_usuario) VALUES (?, ?, 1)');
    $stmt->execute([$password, 'tenancy-smoke-' . bin2hex(random_bytes(4)) . '@example.test']);
    $userId = (int) $pdo->lastInsertId();

    $link = $pdo->prepare('INSERT INTO academia_usuario (academia_id, usuario_id, is_principal, ativo) VALUES (?, ?, ?, 1)');
    $link->execute([$academyA, $userId, 1]);
    $link->execute([$academyB, $userId, 0]);

    $plan = $pdo->prepare('INSERT INTO plano (tipo, duracao, preco, descricao, duriasDias, academia_id) VALUES (?, ?, ?, ?, ?, ?)');
    $plan->execute(['Plano A', '30 dias', 10.00, 'isolamento A', 30, $academyA]);
    $plan->execute(['Plano B', '30 dias', 20.00, 'isolamento B', 30, $academyB]);

    SessionManager::start();
    $_SESSION['user_id'] = $userId;

    AcademyContext::clear();
    AcademyContext::select($academyA);
    $plansA = (new PlanoRepository())->all();
    if (count($plansA) !== 1 || $plansA[0]['tipo'] !== 'Plano A') {
        throw new RuntimeException('Falha: academia A enxergou registros fora do próprio contexto.');
    }

    AcademyContext::select($academyB);
    $plansB = (new PlanoRepository())->all();
    if (count($plansB) !== 1 || $plansB[0]['tipo'] !== 'Plano B') {
        throw new RuntimeException('Falha: academia B enxergou registros fora do próprio contexto.');
    }

    echo "OK: isolamento multiacademia validado.\n";
} finally {
    $pdo->rollBack();
    AcademyContext::clear();
}
