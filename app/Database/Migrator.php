<?php

namespace App\Database;

use PDO;
use RuntimeException;

final class Migrator
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly string $directory
    ) {
    }

    public function migrate(): array
    {
        $this->ensureRepository();
        $executed = $this->executed();
        $applied = [];

        foreach ($this->upFiles() as $file) {
            $name = basename($file, '.up.sql');
            if (isset($executed[$name])) {
                continue;
            }

            $this->executeFile($file);
            $statement = $this->pdo->prepare('INSERT INTO schema_migrations (migration, batch) VALUES (?, ?)');
            $statement->execute([$name, $this->nextBatch()]);
            $applied[] = $name;
        }

        return $applied;
    }

    public function rollback(): array
    {
        $this->ensureRepository();
        $batch = (int) $this->pdo->query('SELECT COALESCE(MAX(batch), 0) FROM schema_migrations')->fetchColumn();
        if ($batch === 0) {
            return [];
        }

        $statement = $this->pdo->prepare('SELECT migration FROM schema_migrations WHERE batch = ? ORDER BY id DESC');
        $statement->execute([$batch]);
        $rolledBack = [];

        foreach ($statement->fetchAll(PDO::FETCH_COLUMN) as $migration) {
            $downFile = $this->directory . '/' . $migration . '.down.sql';
            if (!is_file($downFile)) {
                throw new RuntimeException("Rollback ausente para {$migration}.");
            }

            $this->executeFile($downFile);
            $delete = $this->pdo->prepare('DELETE FROM schema_migrations WHERE migration = ?');
            $delete->execute([$migration]);
            $rolledBack[] = $migration;
        }

        return $rolledBack;
    }

    public function status(): array
    {
        $this->ensureRepository();
        $executed = $this->executed();
        $status = [];

        foreach ($this->upFiles() as $file) {
            $name = basename($file, '.up.sql');
            $status[$name] = isset($executed[$name]) ? 'executed' : 'pending';
        }

        return $status;
    }

    private function ensureRepository(): void
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INT UNSIGNED NOT NULL,
            executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    private function executed(): array
    {
        $rows = $this->pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
        return array_fill_keys($rows, true);
    }

    private function nextBatch(): int
    {
        return (int) $this->pdo->query('SELECT COALESCE(MAX(batch), 0) + 1 FROM schema_migrations')->fetchColumn();
    }

    private function upFiles(): array
    {
        $files = glob($this->directory . '/*.up.sql') ?: [];
        sort($files, SORT_STRING);
        return $files;
    }

    private function executeFile(string $file): void
    {
        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new RuntimeException("Não foi possível ler {$file}.");
        }

        $sql = $this->normalizeLegacySql($sql);
        $statements = preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: [];

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement !== '') {
                $this->pdo->exec($statement);
            }
        }
    }

    private function normalizeLegacySql(string $sql): string
    {
        $database = (string) ($_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'gym_genesis');
        $sql = preg_replace('/CREATE SCHEMA IF NOT EXISTS `[^`]+`[^;]*;/i', '', $sql) ?? $sql;
        $sql = preg_replace('/USE `[^`]+`\s*;/i', '', $sql) ?? $sql;
        $sql = str_replace('`gym_genesis`.', '', $sql);
        $sql = str_replace('DEFAULT CHARACTER SET = utf8', 'DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci', $sql);

        return str_replace('CREATE SCHEMA IF NOT EXISTS `' . $database . '`', '', $sql);
    }
}
