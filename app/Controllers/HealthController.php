<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Database;

final class HealthController
{
    public function live(): void
    {
        $this->respond(200, ['status' => 'ok', 'service' => 'gym-genesis']);
    }

    public function ready(): void
    {
        try {
            Database::getConnection()->query('SELECT 1')->fetchColumn();
            $this->respond(200, ['status' => 'ready', 'database' => 'ok']);
        } catch (\Throwable) {
            $this->respond(503, ['status' => 'not_ready', 'database' => 'unavailable']);
        }
    }

    private function respond(int $status, array $payload): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
