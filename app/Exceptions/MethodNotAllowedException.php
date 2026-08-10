<?php

namespace App\Exceptions;

final class MethodNotAllowedException extends HttpException
{
    public function __construct(array $allowedMethods)
    {
        parent::__construct(405, 'Método HTTP não permitido.', ['Allow' => implode(', ', array_values(array_unique($allowedMethods)))]);
    }
}
