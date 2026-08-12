<?php

declare(strict_types=1);

namespace App\Exceptions;

final class AuthenticationException extends HttpException
{
    public function __construct(string $message = 'Autenticação necessária.')
    {
        parent::__construct(401, $message, ['WWW-Authenticate' => 'Bearer']);
    }
}
