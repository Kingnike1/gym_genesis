<?php

use App\Routes\Router;

// Rotas públicas
Router::get(
    uri: '/',
    callback: function () {
        echo '<h1>Bem-vindo à Gym Genesis!</h1>';
        echo '<p>Esta é a página inicial da aplicação refatorada.</p>';
    }
);

Router::get(
    uri: '/home',
    callback: function () {
        echo '<h1>Bem-vindo à Gym Genesis!</h1>';
        echo '<p>Esta é a página inicial da aplicação refatorada.</p>';
    }
);

// Rotas de autenticação
Router::get(
    uri: '/login',
    callback: function () {
        echo '<h1>Página de Login</h1>';
    }
);

Router::post(
    uri: '/login',
    callback: 'AuthController@login'
);

Router::get(
    uri: '/logout',
    callback: 'AuthController@logout'
);

// Rotas do Administrador
Router::get(
    uri: '/admin/dashboard',
    callback: 'AdminDashboardController@index'
);

// Rotas do Professor
Router::get(
    uri: '/professor/dashboard',
    callback: 'ProfessorDashboardController@index'
);

// Rotas do Aluno
Router::get(
    uri: '/student/dashboard',
    callback: 'StudentDashboardController@index'
);

// Exemplo de rota com parâmetro (ainda não implementado no Router, mas para referência futura)
// Router::get('/users/{id}', 'UserController@show');

// Rotas de Gerenciamento de Usuários (Admin)
Router::get(
    uri: '/admin/users',
    callback: 'UserController@index'
);

Router::get(
    uri: '/admin/users/create',
    callback: 'UserController@create'
);

Router::post(
    uri: '/admin/users',
    callback: 'UserController@store'
);

Router::get(
    uri: '/admin/users/edit/{id}',
    callback: 'UserController@edit'
);

Router::post(
    uri: '/admin/users/{id}',
    callback: 'UserController@update'
);

Router::get(
    uri: '/admin/users/delete/{id}',
    callback: 'UserController@delete'
);

// Rotas de Gerenciamento de Planos (Admin)
Router::get(
    uri: '/admin/plans',
    callback: 'PlanController@index'
);

Router::get(
    uri: '/admin/plans/create',
    callback: 'PlanController@create'
);

Router::post(
    uri: '/admin/plans',
    callback: 'PlanController@store'
);

Router::get(
    uri: '/admin/plans/edit/{id}',
    callback: 'PlanController@edit'
);

Router::post(
    uri: '/admin/plans/{id}',
    callback: 'PlanController@update'
);

Router::get(
    uri: '/admin/plans/delete/{id}',
    callback: 'PlanController@delete'
);

// Rotas de Gerenciamento de Produtos (Admin)
Router::get(
    uri: '/admin/products',
    callback: 'ProductController@index'
);

Router::get(
    uri: '/admin/products/create',
    callback: 'ProductController@create'
);

Router::post(
    uri: '/admin/products',
    callback: 'ProductController@store'
);

Router::get(
    uri: '/admin/products/edit/{id}',
    callback: 'ProductController@edit'
);

Router::post(
    uri: '/admin/products/{id}',
    callback: 'ProductController@update'
);

Router::get(
    uri: '/admin/products/delete/{id}',
    callback: 'ProductController@delete'
);

// Rotas de Gerenciamento de Pedidos (Admin)
Router::get(
    uri: '/admin/orders',
    callback: 'OrderController@index'
);

Router::get(
    uri: '/admin/orders/{id}',
    callback: 'OrderController@show'
);

Router::post(
    uri: '/admin/orders/{id}/status',
    callback: 'OrderController@updateStatus'
);

// Rotas de Gerenciamento de Treinos (Professor)
Router::get(
    uri: '/professor/treinos',
    callback: 'TreinoController@index'
);

Router::get(
    uri: '/professor/treinos/create',
    callback: 'TreinoController@create'
);

Router::post(
    uri: '/professor/treinos',
    callback: 'TreinoController@store'
);

Router::get(
    uri: '/professor/treinos/edit/{id}',
    callback: 'TreinoController@edit'
);

Router::post(
    uri: '/professor/treinos/{id}',
    callback: 'TreinoController@update'
);

Router::get(
    uri: '/professor/treinos/delete/{id}',
    callback: 'TreinoController@delete'
);

// Rotas de Gerenciamento de Dietas (Professor)
Router::get(
    uri: '/professor/dietas',
    callback: 'DietaController@index'
);

Router::get(
    uri: '/professor/dietas/create',
    callback: 'DietaController@create'
);

Router::post(
    uri: '/professor/dietas',
    callback: 'DietaController@store'
);

Router::get(
    uri: '/professor/dietas/edit/{id}',
    callback: 'DietaController@edit'
);

Router::post(
    uri: '/professor/dietas/{id}',
    callback: 'DietaController@update'
);

Router::get(
    uri: '/professor/dietas/delete/{id}',
    callback: 'DietaController@delete'
);

// Rotas do Dashboard do Aluno

Router::get(
    uri: '/student/treinos',
    callback: 'StudentTreinoController@index'
);

Router::get(
    uri: '/student/treinos/{id}',
    callback: 'StudentTreinoController@show'
);

Router::get(
    uri: '/student/dietas',
    callback: 'StudentDietaController@index'
);

Router::get(
    uri: '/student/dietas/{id}',
    callback: 'StudentDietaController@show'
);

// Rotas de Perfil do Aluno
Router::get(
    uri: '/student/perfil',
    callback: 'StudentProfileController@show'
);

Router::post(
    uri: '/student/perfil',
    callback: 'StudentProfileController@update'
);

// Rotas de Progresso do Aluno
Router::get(
    uri: '/student/progresso',
    callback: 'StudentProgressController@index'
);

Router::get(
    uri: '/student/avaliacoes',
    callback: 'StudentProgressController@avaliacoes'
);

Router::get(
    uri: '/student/avaliacoes/create',
    callback: 'StudentProgressController@create'
);

Router::post(
    uri: '/student/avaliacoes',
    callback: 'StudentProgressController@store'
);

Router::get(
    uri: '/student/avaliacoes/{id}',
    callback: 'StudentProgressController@show'
);

Router::get(
    uri: '/student/avaliacoes/{id}/edit',
    callback: 'StudentProgressController@edit'
);

Router::post(
    uri: '/student/avaliacoes/{id}',
    callback: 'StudentProgressController@update'
);

Router::get(
    uri: '/student/avaliacoes/{id}/delete',
    callback: 'StudentProgressController@delete'
);
