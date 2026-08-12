<?php

namespace App\Routes;

use App\Container\Container;
use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\NotFoundException;
use RuntimeException;

final class Router
{
    private const OVERRIDABLE_METHODS = ['PUT', 'PATCH', 'DELETE'];
    private static array $routes = [];
    private static string $groupPrefix = '';
    private static array $groupMiddleware = [];
    private static ?Container $container = null;

    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }
    public static function get(string $uri, callable|string $callback, array $middleware = []): void
    {
        self::add('GET', $uri, $callback, $middleware);
    }
    public static function post(string $uri, callable|string $callback, array $middleware = []): void
    {
        self::add('POST', $uri, $callback, $middleware);
    }
    public static function put(string $uri, callable|string $callback, array $middleware = []): void
    {
        self::add('PUT', $uri, $callback, $middleware);
    }
    public static function patch(string $uri, callable|string $callback, array $middleware = []): void
    {
        self::add('PATCH', $uri, $callback, $middleware);
    }
    public static function delete(string $uri, callable|string $callback, array $middleware = []): void
    {
        self::add('DELETE', $uri, $callback, $middleware);
    }

    public static function group(string $prefix, array $middleware, callable $routes): void
    {
        $previousPrefix = self::$groupPrefix;
        $previousMiddleware = self::$groupMiddleware;
        self::$groupPrefix = self::normalizePath($previousPrefix . '/' . trim($prefix, '/'));
        self::$groupMiddleware = [...$previousMiddleware, ...$middleware];
        try {
            $routes();
        } finally {
            self::$groupPrefix = $previousPrefix;
            self::$groupMiddleware = $previousMiddleware;
        }
    }

    public static function url(string $path = '/'): string
    {
        $basePath = self::basePath();
        $path = self::normalizePath($path);
        return ($basePath === '/' ? '' : $basePath) . $path;
    }

    public static function dispatch(): void
    {
        $method = self::requestMethod();
        $path = self::requestPath();
        $allowedMethods = [];

        foreach (self::$routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            if ($route['method'] !== $method) {
                $allowedMethods[] = $route['method'];
                continue;
            }

            $parameters = [];
            foreach ($route['parameters'] as $name) {
                $parameters[] = $matches[$name] ?? null;
            }
            foreach ($route['middleware'] as $middleware) {
                self::invoke($middleware, $parameters);
            }
            self::invoke($route['callback'], $parameters);
            return;
        }

        if ($allowedMethods !== []) {
            throw new MethodNotAllowedException($allowedMethods);
        }
        throw new NotFoundException('Página não encontrada.');
    }

    private static function add(string $method, string $uri, callable|string $callback, array $middleware): void
    {
        $path = self::normalizePath(self::$groupPrefix . '/' . trim($uri, '/'));
        [$pattern, $parameters] = self::compilePattern($path);
        self::$routes[] = ['method' => $method, 'pattern' => $pattern, 'parameters' => $parameters, 'callback' => $callback, 'middleware' => [...self::$groupMiddleware, ...$middleware]];
    }

    private static function compilePattern(string $path): array
    {
        $parameters = [];
        $pattern = '';
        $offset = 0;
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/', $path, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($matches as $match) {
            $token = $match[0][0];
            $position = $match[0][1];
            $name = $match[1][0];
            $constraint = isset($match[2][0]) && $match[2][0] !== '' ? $match[2][0] : '[^/]+';
            $pattern .= preg_quote(substr($path, $offset, $position - $offset), '#');
            $pattern .= '(?P<' . $name . '>' . $constraint . ')';
            $parameters[] = $name;
            $offset = $position + strlen($token);
        }
        $pattern .= preg_quote(substr($path, $offset), '#');
        return ['#^' . $pattern . '$#', $parameters];
    }

    private static function invoke(callable|string $handler, array $parameters): void
    {
        if (is_callable($handler)) {
            $handler(...$parameters);
            return;
        }
        if (!str_contains($handler, '@')) {
            throw new RuntimeException('Handler de rota inválido: ' . $handler);
        }
        [$class, $method] = explode('@', $handler, 2);
        $class = str_contains($class, '\\') ? $class : 'App\\Controllers\\' . $class;
        if (!class_exists($class) || !method_exists($class, $method)) {
            throw new RuntimeException('Handler de rota não encontrado: ' . $handler);
        }
        $instance = self::$container?->get($class) ?? new $class();
        $instance->{$method}(...$parameters);
    }

    private static function requestMethod(): string
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if ($method !== 'POST') {
            return $method;
        }
        $override = strtoupper((string) ($_POST['_method'] ?? ''));
        return in_array($override, self::OVERRIDABLE_METHODS, true) ? $override : $method;
    }

    private static function requestPath(): string
    {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
        $basePath = self::basePath();
        if ($basePath !== '/' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }
        return self::normalizePath($path);
    }

    private static function basePath(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $basePath = dirname($scriptName);
        return self::normalizePath($basePath === '.' ? '/' : $basePath);
    }

    private static function normalizePath(string $path): string
    {
        $path = '/' . trim(preg_replace('#/+#', '/', $path) ?? '/', '/');
        return $path === '' ? '/' : $path;
    }
}
