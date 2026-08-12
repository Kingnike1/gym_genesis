<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\HttpException;
use App\Exceptions\ValidationException;
use App\Logging\RequestContext;
use Psr\Log\LoggerInterface;

final class ErrorHandler
{
    private static ?LoggerInterface $logger = null;

    public static function register(bool $debug = false, ?LoggerInterface $logger = null): void
    {
        self::$logger = $logger;
        set_exception_handler(static fn (\Throwable $e) => self::handle($e, $debug));
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
    }

    public static function handle(\Throwable $e, bool $debug = false): void
    {
        $status = match (true) {
            $e instanceof HttpException => $e->statusCode,
            $e instanceof \InvalidArgumentException => 422,
            $e instanceof \DomainException => 409,
            default => 500,
        };

        $headers = $e instanceof HttpException ? $e->headers : [];
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
        http_response_code($status);

        $requestId = RequestContext::id();
        $context = [
            'request_id' => $requestId,
            'exception' => $e::class,
            'status' => $status,
            'path' => parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH),
        ];
        if (self::$logger !== null) {
            self::$logger->error($e->getMessage(), $context);
        } else {
            error_log(sprintf('[%s] %s: %s', $requestId, $e::class, $e->getMessage()));
        }

        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
        $wantsJson = str_contains($accept, 'application/json') || str_starts_with((string) ($_SERVER['REQUEST_URI'] ?? ''), '/api/');
        $publicMessage = $status >= 500 && !$debug ? 'Ocorreu um erro interno.' : $e->getMessage();

        if ($wantsJson) {
            header('Content-Type: application/json; charset=utf-8');
            $payload = ['error' => ['status' => $status, 'message' => $publicMessage, 'request_id' => $requestId]];
            if ($e instanceof ValidationException) {
                $payload['error']['fields'] = $e->errors;
            }
            if ($debug && $status >= 500) {
                $payload['error']['exception'] = $e::class;
            }
            echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        $safe = htmlspecialchars($publicMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeRequest = htmlspecialchars($requestId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo "<h1>{$status}</h1><p>{$safe}</p><small>Referência: {$safeRequest}</small>";
        if ($debug && $status >= 500) {
            echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
        }
    }
}
