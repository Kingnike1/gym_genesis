<?php

namespace App\Controllers;

use App\Helpers\SecurityHelper;
use App\Middleware\AuthMiddleware;
use App\Routes\Router;
use App\Tenancy\AcademyAudit;
use App\Tenancy\AcademyContext;
use RuntimeException;

final class AcademyContextController extends Controller
{
    public function select(): void
    {
        if (!SecurityHelper::verifyCSRFToken((string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            echo 'Token CSRF inválido.';
            return;
        }

        $academyId = filter_var($_POST['academia_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($academyId === false) {
            http_response_code(422);
            echo 'Academia inválida.';
            return;
        }

        try {
            AcademyContext::select((int) $academyId);
            AcademyAudit::record('context.select', 'academia', (int) $academyId);
        } catch (RuntimeException) {
            http_response_code(403);
            echo 'Acesso à academia negado.';
            return;
        }

        $destinations = [
            1 => '/admin/dashboard',
            2 => '/professor/dashboard',
            3 => '/student/dashboard',
        ];

        header('Location: ' . Router::url($destinations[AuthMiddleware::getUserType() ?? 0] ?? '/home'));
        exit();
    }
}
