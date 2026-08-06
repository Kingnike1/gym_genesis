<?php

declare(strict_types=1);

use App\Services\Database;

require dirname(__DIR__) . '/bootstrap/app.php';

$environment = (string) ($_ENV['APP_ENV'] ?? 'production');
if ($environment === 'production' && !in_array('--force', $argv, true)) {
    throw new RuntimeException('Seed em produção exige --force.');
}

$directory = dirname(__DIR__) . '/database/seeders';
$files = glob($directory . '/*.sql') ?: [];
sort($files, SORT_STRING);

$pdo = Database::getConnection();
foreach ($files as $file) {
    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException("Não foi possível ler {$file}.");
    }

    $pdo->beginTransaction();
    try {
        foreach (preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: [] as $statement) {
            $statement = trim($statement);
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }
        $pdo->commit();
        echo basename($file) . PHP_EOL;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}

if ($files === []) {
    echo "Nenhum seeder cadastrado." . PHP_EOL;
}
