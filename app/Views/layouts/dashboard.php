<?php

declare(strict_types=1);

use App\Helpers\SecurityHelper;
use App\Routes\Router;

$title = $title ?? 'Dashboard';
$navigation = $navigation ?? [];
$contentView = $contentView ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) $title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> — Gym Genesis</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(Router::url('/assets/css/app.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" aria-label="Navegação principal">
        <p class="brand">Gym Genesis</p>
        <nav>
            <ul class="nav">
                <?php foreach ($navigation as $label => $path) : ?>
                    <li><a href="<?= htmlspecialchars(Router::url($path), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </aside>
    <main class="content" id="main-content">
        <header class="page-header">
            <h1><?= htmlspecialchars((string) $title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
            <form class="logout-form" method="post" action="<?= htmlspecialchars(Router::url('/logout'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SecurityHelper::generateCSRFToken(), ENT_QUOTES, 'UTF-8') ?>">
                <button class="button-danger" type="submit">Sair</button>
            </form>
        </header>
        <?php if (!empty($errorMessage)) :
            ?><div class="alert alert-error" role="alert"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div><?php
        endif; ?>
        <?php if (!empty($successMessage)) :
            ?><div class="alert alert-success" role="status"><?= htmlspecialchars((string) $successMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div><?php
        endif; ?>
        <section class="card">
            <?php if ($contentView !== null && is_file($contentView)) :
                include $contentView;
            else : ?>
                <div class="empty-state">Nenhum conteúdo disponível.</div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
