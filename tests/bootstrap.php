<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/.env.testing')) {
    Dotenv\Dotenv::createImmutable(dirname(__DIR__), '.env.testing')->safeLoad();
}

$_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'testing';
