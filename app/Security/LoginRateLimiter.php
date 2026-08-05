<?php

namespace App\Security;

final class LoginRateLimiter
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_SECONDS = 900;
    private const BLOCK_SECONDS = 900;

    public static function tooManyAttempts(string $key): bool
    {
        $state = self::read($key);
        return ($state['blocked_until'] ?? 0) > time();
    }

    public static function hit(string $key): void
    {
        $state = self::read($key);
        $now = time();

        if (($state['window_started_at'] ?? 0) < ($now - self::WINDOW_SECONDS)) {
            $state = ['attempts' => 0, 'window_started_at' => $now, 'blocked_until' => 0];
        }

        $state['attempts']++;
        if ($state['attempts'] >= self::MAX_ATTEMPTS) {
            $state['blocked_until'] = $now + self::BLOCK_SECONDS;
        }

        self::write($key, $state);
    }

    public static function clear(string $key): void
    {
        @unlink(self::path($key));
    }

    private static function read(string $key): array
    {
        $path = self::path($key);
        if (!is_file($path)) {
            return ['attempts' => 0, 'window_started_at' => time(), 'blocked_until' => 0];
        }

        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : ['attempts' => 0, 'window_started_at' => time(), 'blocked_until' => 0];
    }

    private static function write(string $key, array $state): void
    {
        file_put_contents(self::path($key), json_encode($state, JSON_THROW_ON_ERROR), LOCK_EX);
    }

    private static function path(string $key): string
    {
        return sys_get_temp_dir() . '/gym_genesis_login_' . hash('sha256', $key) . '.json';
    }
}
