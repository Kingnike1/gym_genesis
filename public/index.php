<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Routes\Router;
use Dotenv\Dotenv;

$rootPath = dirname(__DIR__);

if (is_file($rootPath . '/.env')) {
    Dotenv::createImmutable($rootPath)->safeLoad();
}

$environment = strtolower((string) ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'production'));
$debug = filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL);
$forceHttps = filter_var($_ENV['APP_FORCE_HTTPS'] ?? getenv('APP_FORCE_HTTPS') ?: false, FILTER_VALIDATE_BOOL);
$appUrl = rtrim((string) ($_ENV['APP_URL'] ?? getenv('APP_URL') ?: ''), '/');

ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

if ($environment === 'production') {
    $requiredVariables = ['APP_URL', 'APP_SECRET', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];

    foreach ($requiredVariables as $variable) {
        $value = $_ENV[$variable] ?? getenv($variable);

        if (!is_string($value) || trim($value) === '') {
            error_log("Missing required production environment variable: {$variable}");
            http_response_code(500);
            exit('A aplicação não está configurada corretamente.');
        }
    }

    if (strtolower((string) ($_ENV['DB_USER'] ?? getenv('DB_USER'))) === 'root') {
        error_log('The application database user must not be root.');
        http_response_code(500);
        exit('A aplicação não está configurada corretamente.');
    }
}

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443
    || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';

if ($environment === 'production' && $forceHttps && !$isHttps) {
    if ($appUrl === '' || !str_starts_with(strtolower($appUrl), 'https://')) {
        error_log('APP_URL must use HTTPS when APP_FORCE_HTTPS is enabled.');
        http_response_code(500);
        exit('A aplicação não está configurada corretamente.');
    }

    $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    header('Location: ' . $appUrl . $requestUri, true, 301);
    exit;
}

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    if ($isHttps && $environment === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

require_once $rootPath . '/routes/web.php';

Router::dispatch();
