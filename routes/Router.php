<?php

namespace App\Routes;

class Router
{
    protected static $routes = [];

    public static function get($uri, $callback)
    {
        $uri = trim($uri, '/');
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post($uri, $callback)
    {
        $uri = trim($uri, '/');
        self::$routes['POST'][$uri] = $callback;
    }

    public static function dispatch()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // Remove o prefixo /gym_genesis se a aplicação estiver em um subdiretório
        $basePath = 'gym_genesis'; // Ajuste conforme o caminho real no servidor web
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
            $uri = trim($uri, '/');
        }

        foreach (self::$routes[$method] ?? [] as $route => $callback) {
            // Converte a rota para uma expressão regular para capturar parâmetros
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches); // Remove a correspondência completa da URI

                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                } elseif (is_string($callback)) {
                    list($controller, $method) = explode('@', $callback);
                    $controller = 'App\\Controllers\\' . $controller;
                    if (class_exists($controller)) {
                        $controllerInstance = new $controller();
                        if (method_exists($controllerInstance, $method)) {
                            call_user_func_array([$controllerInstance, $method], $matches);
                        } else {
                            self::handleNotFound();
                        }
                    } else {
                        self::handleNotFound();
                    }
                } else {
                    self::handleNotFound();
                }
                return; // Rota encontrada e despachada
            }
        }

        self::handleNotFound(); // Nenhuma rota encontrada
    }

    protected static function handleNotFound()
    {
        http_response_code(404);
        echo '<h1>404 - Página Não Encontrada</h1>';
    }
}
