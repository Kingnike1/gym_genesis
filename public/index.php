<?php

declare(strict_types=1);

use App\Routes\Router;
use Psr\Log\LoggerInterface;

$requestStartedAt = hrtime(true);
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
$isHealthCheck = in_array($path, ['/health', '/ready'], true);

// Emit an early line before the application bootstrap so Render logs still
// show where a request stopped even when bootstrap itself becomes slow.
if (!$isHealthCheck) {
    error_log(sprintf('[trace] request.start method=%s path=%s', $method, $path));
}

$bootstrapStartedAt = hrtime(true);
$container = require __DIR__ . '/../bootstrap/app.php';
/** @var LoggerInterface $logger */
$logger = $container->get(LoggerInterface::class);
$bootstrapMs = (hrtime(true) - $bootstrapStartedAt) / 1_000_000;

if (!$isHealthCheck) {
    $logger->info('HTTP trace: bootstrap.ok', [
        'method' => $method,
        'path' => $path,
        'duration_ms' => round($bootstrapMs, 2),
    ]);
}

register_shutdown_function(static function () use ($logger, $requestStartedAt, $method, $path, $isHealthCheck): void {
    $durationMs = (hrtime(true) - $requestStartedAt) / 1_000_000;
    $status = http_response_code();
    $lastError = error_get_last();

    if (!$isHealthCheck || $status >= 500 || $durationMs >= 750) {
        $context = [
            'method' => $method,
            'path' => $path,
            'status' => $status,
            'duration_ms' => round($durationMs, 2),
            'memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ];

        if ($lastError !== null && in_array($lastError['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            $context['fatal_error'] = $lastError['message'];
        }

        $logger->info('HTTP trace: request.done', $context);
    }
});

Router::setContainer($container);
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

if (!$isHealthCheck) {
    $logger->info('HTTP trace: routes.loaded', [
        'method' => $method,
        'path' => $path,
    ]);
    $logger->info('HTTP trace: dispatch.start', [
        'method' => $method,
        'path' => $path,
    ]);
}

Router::dispatch();

if (!$isHealthCheck) {
    $logger->info('HTTP trace: dispatch.ok', [
        'method' => $method,
        'path' => $path,
        'status' => http_response_code(),
    ]);
}

$durationMs = (hrtime(true) - $requestStartedAt) / 1_000_000;
$slowThresholdMs = max(1, (int) ($_ENV['SLOW_REQUEST_MS'] ?? 750));
if ($durationMs >= $slowThresholdMs) {
    $logger->warning('Slow HTTP request', [
        'duration_ms' => round($durationMs, 2),
        'method' => $method,
        'path' => $path,
    ]);
}
