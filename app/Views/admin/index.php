<?php
// Usar os valores passados pelo controller, ou defaults se não existirem
$title = $title ?? "Dashboard do Administrador";
$contentView = $contentView ?? __DIR__ . "/dashboard_content.php";
include __DIR__ . "/layout.php";
?>
