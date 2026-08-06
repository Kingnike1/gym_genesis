<?php

namespace App\Controllers;

use App\DTOs\CreateUserData;
use App\DTOs\UpdateUserData;
use App\Enums\UserRole;
use App\Services\UserService;

final class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function index(): void
    {
        $this->render('admin/users/index', ['users' => $this->userService->getAllUsers()]);
    }

    public function create(): void
    {
        $this->render('admin/users/create', ['roles' => UserRole::cases()]);
    }

    public function store(): void
    {
        $data = new CreateUserData(
            email: (string) ($_POST['email'] ?? ''),
            password: (string) ($_POST['password'] ?? ''),
            role: UserRole::fromInput((int) ($_POST['user_type'] ?? UserRole::Student->value)),
        );

        $userId = $this->userService->registerUser($data);
        if ($userId !== null) {
            $this->redirect('/admin/users');
            return;
        }

        $this->render('admin/users/create', ['roles' => UserRole::cases(), 'errorMessage' => 'Erro ao cadastrar usuário.']);
    }

    public function edit(int $id): void
    {
        $user = $this->userService->getUserById($id);
        if ($user === null) {
            $this->handleNotFound();
        }

        $this->render('admin/users/edit', ['user' => $user, 'roles' => UserRole::cases()]);
    }

    public function update(int $id): void
    {
        $data = new UpdateUserData(
            email: (string) ($_POST['email'] ?? ''),
            password: ($_POST['password'] ?? '') !== '' ? (string) $_POST['password'] : null,
            role: UserRole::fromInput((int) ($_POST['user_type'] ?? UserRole::Student->value)),
            active: (string) ($_POST['active'] ?? '1') === '1',
        );

        if ($this->userService->updateUser($id, $data)) {
            $this->redirect('/admin/users');
            return;
        }

        $this->render('admin/users/edit', [
            'user' => $this->userService->getUserById($id),
            'roles' => UserRole::cases(),
            'errorMessage' => 'Erro ao atualizar usuário.',
        ]);
    }

    public function delete(int $id): void
    {
        $this->userService->deleteUser($id);
        $this->redirect('/admin/users');
    }

    protected function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Usuário Não Encontrado</h1>';
        exit();
    }
}
