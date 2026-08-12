<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Middleware\AuthMiddleware;
use App\Repositories\AlunoRepository;
use App\Tenancy\AcademyContext;

final class ApiController
{
    public function __construct(private readonly AlunoRepository $students)
    {
    }

    public function me(): void
    {
        $this->json([
            'data' => [
                'user_id' => AuthMiddleware::getUserId(),
                'academy_id' => AcademyContext::id(),
                'unit_id' => AcademyContext::unitId(),
                'role' => AcademyContext::role(),
            ],
        ]);
    }

    public function students(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 25)));
        $this->json($this->students->paginate($page, $perPage));
    }

    private function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
