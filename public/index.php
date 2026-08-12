<?php

declare(strict_types=1);

use App\Routes\Router;
use Psr\Log\LoggerInterface;

$startedAt = hrtime(true);
$container = require __DIR__ . '/../bootstrap/app.php';

Router::setContainer($container);
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';
Router::dispatch();

$durationMs = (hrtime(true) - $startedAt) / 1_000_000;
$slowThresholdMs = max(1, (int) ($_ENV['SLOW_REQUEST_MS'] ?? 750));
if ($durationMs >= $slowThresholdMs) {
    $container->get(LoggerInterface::class)->warning('Slow HTTP request', [
        'duration_ms' => round($durationMs, 2),
        'method' => (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
        'path' => parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH),
    ]);
}
