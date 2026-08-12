<?php

namespace App\Exceptions;

final class AuthorizationException extends HttpException
{
    public function __construct(string $message = 'Acesso não autorizado.')
    {
        parent::__construct(403, $message);
    }
}
