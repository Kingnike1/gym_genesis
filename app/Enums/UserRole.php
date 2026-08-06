<?php

namespace App\Enums;

enum UserRole: int
{
    case Admin = 1;
    case Professor = 2;
    case Student = 3;

    public static function fromInput(int $value): self
    {
        return self::tryFrom($value) ?? self::Student;
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Professor => 'Professor',
            self::Student => 'Aluno',
        };
    }
}
