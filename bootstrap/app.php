<?php

use App\Container\Container;
use App\Http\ErrorHandler;
use App\Storage\LocalStorage;
use App\Storage\StorageInterface;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

$appEnv = (string) ($_ENV['APP_ENV'] ?? 'production');
$appDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
date_default_timezone_set((string) ($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo'));

$logger = require __DIR__ . '/logging.php';
ErrorHandler::register($appDebug, $logger);

$container = new Container();
$container->instance(Container::class, $container);
$container->instance(LoggerInterface::class, $logger);
$container->singleton(StorageInterface::class, static fn () => new LocalStorage());

return $container;
