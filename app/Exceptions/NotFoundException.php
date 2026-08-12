<?php

namespace App\Exceptions;

final class NotFoundException extends HttpException
{
    public function __construct(string $message = 'Recurso não encontrado.')
    {
        parent::__construct(404, $message);
    }
}
