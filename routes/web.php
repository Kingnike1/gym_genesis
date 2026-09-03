<?php

use App\Middleware\AcademyContextMiddleware;
use App\Middleware\AuthMiddleware;
use App\Routes\Router;

$academyContext = static fn () => AcademyContextMiddleware::handle();
$adminOnly = [static fn () => AuthMiddleware::requireUserType(1), $academyContext];
$professorOnly = [static fn () => AuthMiddleware::requireUserType(2), $academyContext];
$studentOnly = [static fn () => AuthMiddleware::requireUserType(3), $academyContext];

Router::get('/health', 'HealthController@live');
Router::get('/ready', 'HealthController@ready');

Router::get('/teste', static function (): void {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Gym Genesis - Teste</title></head>';
    echo '<body style="font-family:Arial,sans-serif;max-width:720px;margin:60px auto;padding:20px">';
    echo '<h1>Gym Genesis está online ✅</h1>';
    echo '<p>Se você está vendo esta página, o PHP, Apache e o roteamento HTTP estão respondendo.</p>';
    echo '<p><a href="/health">Testar /health</a></p>';
    echo '<p><a href="/ready">Testar /ready (inclui banco)</a></p>';
    echo '<p><a href="/login">Abrir login do sistema</a></p>';
    echo '</body></html>';
});

Router::get('/', static function (): void {
    echo '<h1>Bem-vindo à Gym Genesis!</h1>';
    echo '<p>Esta é a página inicial da aplicação refatorada.</p>';
    echo '<p><a href="/teste">Abrir página de diagnóstico</a></p>';
});
Router::get('/home', static function (): void {
    echo '<h1>Bem-vindo à Gym Genesis!</h1>';
    echo '<p>Esta é a página inicial da aplicação refatorada.</p>';
});

Router::get('/login', 'AuthController@login');
Router::post('/login', 'AuthController@login');
Router::get('/password/forgot', 'PasswordResetController@request');
Router::post('/password/forgot', 'PasswordResetController@request');
Router::get('/password/reset', 'PasswordResetController@reset');
Router::post('/password/reset', 'PasswordResetController@reset');
Router::post('/logout', 'AuthController@logout', [static fn () => AuthMiddleware::requireAuth()]);
Router::post('/academy/select', 'AcademyContextController@select', [static fn () => AuthMiddleware::requireAuth()]);

Router::group('/admin', $adminOnly, static function (): void {
    Router::get('/dashboard', 'AdminDashboardController@index');
    Router::get('/users', 'UserController@index');
    Router::get('/users/create', 'UserController@create');
    Router::post('/users', 'UserController@store');
    Router::get('/users/{id:\d+}/edit', 'UserController@edit');
    Router::put('/users/{id:\d+}', 'UserController@update');
    Router::delete('/users/{id:\d+}', 'UserController@delete');
    Router::get('/plans', 'PlanController@index');
    Router::get('/plans/create', 'PlanController@create');
    Router::post('/plans', 'PlanController@store');
    Router::get('/plans/{id:\d+}/edit', 'PlanController@edit');
    Router::put('/plans/{id:\d+}', 'PlanController@update');
    Router::delete('/plans/{id:\d+}', 'PlanController@delete');
    Router::get('/products', 'ProductController@index');
    Router::get('/products/create', 'ProductController@create');
    Router::post('/products', 'ProductController@store');
    Router::get('/products/{id:\d+}/edit', 'ProductController@edit');
    Router::put('/products/{id:\d+}', 'ProductController@update');
    Router::delete('/products/{id:\d+}', 'ProductController@delete');
    Router::get('/orders', 'OrderController@index');
    Router::get('/orders/{id:\d+}', 'OrderController@show');
    Router::patch('/orders/{id:\d+}/status', 'OrderController@updateStatus');
});

Router::group('/professor', $professorOnly, static function (): void {
    Router::get('/dashboard', 'ProfessorDashboardController@index');
    Router::get('/treinos', 'TreinoController@index');
    Router::get('/treinos/create', 'TreinoController@create');
    Router::post('/treinos', 'TreinoController@store');
    Router::get('/treinos/{id:\d+}/edit', 'TreinoController@edit');
    Router::put('/treinos/{id:\d+}', 'TreinoController@update');
    Router::delete('/treinos/{id:\d+}', 'TreinoController@delete');
    Router::get('/dietas', 'DietaController@index');
    Router::get('/dietas/create', 'DietaController@create');
    Router::post('/dietas', 'DietaController@store');
    Router::get('/dietas/{id:\d+}/edit', 'DietaController@edit');
    Router::put('/dietas/{id:\d+}', 'DietaController@update');
    Router::delete('/dietas/{id:\d+}', 'DietaController@delete');
});

Router::group('/student', $studentOnly, static function (): void {
    Router::get('/dashboard', 'StudentDashboardController@index');
    Router::get('/treinos', 'StudentTreinoController@index');
    Router::get('/treinos/{id:\d+}', 'StudentTreinoController@show');
    Router::post('/treinos/{id:\d+}/executions', 'StudentTreinoController@startExecution');
    Router::post('/treino-executions/{id:\d+}/finish', 'StudentTreinoController@finishExecution');
    Router::get('/dietas', 'StudentDietaController@index');
    Router::get('/dietas/{id:\d+}', 'StudentDietaController@show');
    Router::get('/perfil', 'StudentProfileController@show');
    Router::put('/perfil', 'StudentProfileController@update');
    Router::get('/progresso', 'StudentProgressController@index');
    Router::get('/avaliacoes', 'StudentProgressController@avaliacoes');
    Router::get('/avaliacoes/create', 'StudentProgressController@create');
    Router::post('/avaliacoes', 'StudentProgressController@store');
    Router::get('/avaliacoes/{id:\d+}', 'StudentProgressController@show');
    Router::get('/avaliacoes/{id:\d+}/edit', 'StudentProgressController@edit');
    Router::put('/avaliacoes/{id:\d+}', 'StudentProgressController@update');
    Router::delete('/avaliacoes/{id:\d+}', 'StudentProgressController@delete');
});
