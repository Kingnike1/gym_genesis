<?php

declare(strict_types=1);

use App\Middleware\ApiTokenMiddleware;
use App\Routes\Router;

$apiAuth = static fn () => ApiTokenMiddleware::authenticate();

Router::group('/api/v1', [$apiAuth], static function (): void {
    Router::get('/me', 'App\\Controllers\\Api\\V1\\ApiController@me');
    Router::get('/students', 'App\\Controllers\\Api\\V1\\ApiController@students', [
        static fn () => ApiTokenMiddleware::requireScope('students:read'),
    ]);
});
