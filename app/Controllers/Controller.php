<?php

namespace App\Controllers;

class Controller
{
    protected function render(string $viewPath, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../Views/{$viewPath}.php";
    }

    protected function redirect(string $path): void
    {
        header("Location: /gym_genesis" . $path);
        exit();
    }
}
