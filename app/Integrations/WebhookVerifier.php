<?php

declare(strict_types=1);

namespace App\Integrations;

final class WebhookVerifier
{
    public static function verifyHmacSha256(string $payload, string $signature, string $secret): bool
    {
        if ($secret === '' || $signature === '') {
            return false;
        }
        $expected = hash_hmac('sha256', $payload, $secret);
        $provided = preg_replace('/^sha256=/i', '', trim($signature)) ?? '';
        return hash_equals($expected, strtolower($provided));
    }
}
