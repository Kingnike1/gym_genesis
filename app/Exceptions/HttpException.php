<?php

namespace App\Exceptions;

class HttpException extends \RuntimeException
{
    public function __construct(
        public readonly int $statusCode,
        string $message,
        public readonly array $headers = []
    ) {
        parent::__construct($message);
    }
}
