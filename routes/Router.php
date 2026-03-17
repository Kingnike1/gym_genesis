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
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // 🔥 REMOVE automaticamente /public da URL
        $scriptName = dirname($_SERVER['SCRIPT_NAME']); 
        // Ex: /public

        if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        $uri = trim($uri, '/');

        foreach (self::$routes[$method] ?? [] as $route => $callback) {

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);

            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches);

                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);

                } elseif (is_string($callback)) {

                    list($controller, $action) = explode('@', $callback);
                    $controller = 'App\\Controllers\\' . $controller;

                    if (class_exists($controller)) {
                        $instance = new $controller();

                        if (method_exists($instance, $action)) {
                            call_user_func_array([$instance, $action], $matches);
                            return;
                        }
                    }

                    self::handleNotFound();
                    return;
                }
            }
        }

        self::handleNotFound();
    }

    protected static function handleNotFound()
    {
        http_response_code(404);
        echo '<h1>404 - Página Não Encontrada</h1>';
    }
}