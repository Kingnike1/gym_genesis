<?php

declare(strict_types=1);

namespace App\Logging;

final class RequestContext
{
    private static ?string $id = null;

    public static function id(): string
    {
        return self::$id ??= bin2hex(random_bytes(8));
    }

    public static function reset(): void
    {
        self::$id = null;
    }
}
