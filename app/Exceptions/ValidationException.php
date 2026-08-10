<?php

namespace App\Exceptions;

final class ValidationException extends HttpException
{
    public function __construct(public readonly array $errors, string $message = 'Dados inválidos.')
    {
        parent::__construct(422, $message);
    }
}
