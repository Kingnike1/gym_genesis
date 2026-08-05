<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(string $password, string $email, int $userType): ?int
    {
        return $this->userRepository->create(password_hash($password, PASSWORD_DEFAULT), $email, $userType);
    }

    public function authenticateUser(string $email, string $password): ?array
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user || !password_verify($password, $user['senha'])) {
            return null;
        }

        if (password_needs_rehash($user['senha'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $this->userRepository->update((int) $user['idusuario'], $newHash, $user['email'], (int) $user['tipo_usuario']);
            $user['senha'] = $newHash;
        }

        return $user;
    }

    public function updateUser(int $id, string $password, string $email, int $userType): bool
    {
        $currentUser = $this->userRepository->findById($id);
        if (!$currentUser) {
            return false;
        }

        $passwordHash = $currentUser['senha'];
        if ($password !== '' && !password_verify($password, $currentUser['senha'])) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        return $this->userRepository->update($id, $passwordHash, $email, $userType);
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
        return $this->userRepository->findByEmail($email);
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAllUsers();
    }

    public function updateUserPassword(int $id, string $passwordHash): bool
    {
        $currentUser = $this->userRepository->findById($id);
        return $currentUser
            ? $this->userRepository->update($id, $passwordHash, $currentUser['email'], (int) $currentUser['tipo_usuario'])
            : false;
    }

    public function updateUserEmail(int $id, string $email): bool
    {
        $currentUser = $this->userRepository->findById($id);
        return $currentUser
            ? $this->userRepository->update($id, $currentUser['senha'], $email, (int) $currentUser['tipo_usuario'])
            : false;
    }
}
