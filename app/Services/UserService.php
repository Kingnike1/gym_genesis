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
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this->userRepository->create($passwordHash, $email, $userType);
    }

    public function authenticateUser(string $email, string $password): ?array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user["senha"])) {
            return null; // Autenticação falhou
        }

        return $user; // Retorna os dados do usuário autenticado
    }

    public function updateUser(int $id, string $password, string $email, int $userType): bool
    {
        $currentUser = $this->userRepository->findById($id);
        if (!$currentUser) {
            return false; // Usuário não encontrado
        }

        $passwordHash = $currentUser["senha"];
        // Se uma nova senha for fornecida e for diferente da atual, gera um novo hash
        if (!empty($password) && !password_verify($password, $currentUser["senha"])) {
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
        if (!$currentUser) {
            return false;
        }

        return $this->userRepository->update($id, $passwordHash, $currentUser['email'], $currentUser['tipo_usuario']);
    }

    public function updateUserEmail(int $id, string $email): bool
    {
        $currentUser = $this->userRepository->findById($id);
        if (!$currentUser) {
            return false;
        }

        return $this->userRepository->update($id, $currentUser['senha'], $email, $currentUser['tipo_usuario']);
    }
}
