<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Routes\Router;

// Carregar variáveis de ambiente
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Configurações de erro
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir as definições de rotas
require_once __DIR__ . '/../routes/web.php';

// Despachar a requisição
Router::dispatch();

?>
