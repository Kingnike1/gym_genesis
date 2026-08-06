<?php

namespace App\DTOs;

use App\Enums\UserRole;

final readonly class CreateUserData
{
    public function __construct(
        public string $email,
        public string $password,
        public UserRole $role,
    ) {
    }
}
