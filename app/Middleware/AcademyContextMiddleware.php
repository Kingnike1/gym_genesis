<?php

namespace App\Middleware;

use App\Tenancy\AcademyContext;
use RuntimeException;

final class AcademyContextMiddleware
{
    public static function handle(): void
    {
        try {
            AcademyContext::current();
        } catch (RuntimeException) {
            http_response_code(403);
            echo '<h1>403 - Academia não disponível</h1>';
            exit();
        }
    }
}
