<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Middleware\RouteAuthorizationMiddleware;
use App\Routes\Router;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/security.php';

RouteAuthorizationMiddleware::handle((string) ($_SERVER['REQUEST_URI'] ?? '/'));
Router::dispatch();
