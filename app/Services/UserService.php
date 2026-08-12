<?php

namespace App\Services;

use App\DTOs\CreateUserData;
use App\DTOs\UpdateUserData;
use App\Repositories\UserRepository;

final class UserService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function registerUser(CreateUserData $data): ?int
    {
        return $this->userRepository->create(
            password_hash($data->password, PASSWORD_DEFAULT),
            strtolower(trim($data->email)),
            $data->role
        );
    }

    public function authenticateUser(string $email, string $password): ?array
    {
        $user = $this->userRepository->findForAuthentication(strtolower(trim($email)));
        if (!$user || $user['status'] !== 'ativo' || !password_verify($password, $user['senha'])) {
            return null;
        }

        if (password_needs_rehash($user['senha'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $this->userRepository->updatePasswordHash((int) $user['idusuario'], $newHash);
            $user['senha'] = $newHash;
        }

        $this->userRepository->recordLogin((int) $user['idusuario']);
        return $user;
    }

    public function updateUser(int $id, UpdateUserData $data): bool
    {
        if ($data->password !== null && $data->password !== '') {
            $this->userRepository->updatePasswordHash($id, password_hash($data->password, PASSWORD_DEFAULT));
        }

        return $this->userRepository->update($id, strtolower(trim($data->email)), $data->role, $data->active);
    }

    public function updateUserEmail(int $id, string $email): bool
    {
        $email = strtolower(trim($email));
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('E-mail inválido.');
        }

        $existing = $this->userRepository->findByEmail($email);
        if ($existing !== null && (int) $existing['idusuario'] !== $id) {
            throw new \InvalidArgumentException('E-mail já utilizado por outro usuário.');
        }

        return $this->userRepository->updateEmail($id, $email);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function getUserById(int $id): ?array
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByEmail(string $email): ?array
    {
        return $this->userRepository->findByEmail(strtolower(trim($email)));
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAllUsers();
    }

    public function updateUserPassword(int $id, string $passwordHash): bool
    {
        return $this->userRepository->updatePasswordHash($id, $passwordHash);
    }
}
