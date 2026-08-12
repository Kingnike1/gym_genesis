<?php

declare(strict_types=1);

$navigation = [
    'Dashboard' => '/student/dashboard',
    'Meus Treinos' => '/student/treinos',
    'Minhas Dietas' => '/student/dietas',
    'Progresso' => '/student/progresso',
    'Perfil' => '/student/perfil',
];

include dirname(__DIR__) . '/layouts/dashboard.php';
