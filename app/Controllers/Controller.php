<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Routes\Router;

class Controller
{
    protected function render(string $viewPath, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../Views/{$viewPath}.php";
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . Router::url($path));
        exit();
    }
}
