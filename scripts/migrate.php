<?php

declare(strict_types=1);

use App\Database\Migrator;
use App\Services\Database;

require dirname(__DIR__) . '/bootstrap/app.php';

$command = $argv[1] ?? 'migrate';
$migrator = new Migrator(
    Database::getConnection(),
    dirname(__DIR__) . '/database/migrations'
);

$result = match ($command) {
    'migrate' => $migrator->migrate(),
    'rollback' => $migrator->rollback(),
    'status' => $migrator->status(),
    default => throw new InvalidArgumentException('Use: migrate, rollback ou status.'),
};

foreach ($result as $name => $value) {
    if (is_int($name)) {
        echo $value . PHP_EOL;
        continue;
    }

    echo $name . ': ' . $value . PHP_EOL;
}

if ($result === []) {
    echo "Nenhuma alteração necessária." . PHP_EOL;
}
