<?php

declare(strict_types=1);

$navigation = [
    'Dashboard' => '/admin/dashboard',
    'Usuários' => '/admin/users',
    'Planos' => '/admin/plans',
    'Loja' => '/admin/products',
    'Pedidos' => '/admin/orders',
];

include dirname(__DIR__) . '/layouts/dashboard.php';
