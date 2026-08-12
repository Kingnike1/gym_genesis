<?php

declare(strict_types=1);

namespace App\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public static function create(): LoggerInterface
    {
        $logger = new Logger('gym-genesis');
        $stream = (string) ($_ENV['LOG_STREAM'] ?? 'php://stderr');
        $level = Level::fromName((string) ($_ENV['LOG_LEVEL'] ?? 'info'));
        $logger->pushHandler(new StreamHandler($stream, $level));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        $logger->pushProcessor(static function (array $record): array {
            $record['extra']['request_id'] = RequestContext::id();
            if (isset($_SESSION['user_id'])) {
                $record['extra']['user_id'] = (int) $_SESSION['user_id'];
            }
            if (isset($_SESSION['academy_id'])) {
                $record['extra']['academy_id'] = (int) $_SESSION['academy_id'];
            }
            return $record;
        });
        return $logger;
    }
}
