<?php

namespace App\Container;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $id, callable $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function singleton(string $id, callable $factory): void
    {
        $this->bindings[$id] = function (self $container) use ($id, $factory): object {
            return $this->instances[$id] ??= $factory($container);
        };
    }

    public function instance(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id])) {
            $resolved = ($this->bindings[$id])($this);
            if (!is_object($resolved)) {
                throw new RuntimeException("Binding {$id} deve retornar um objeto.");
            }
            return $resolved;
        }

        return $this->autowire($id);
    }

    private function autowire(string $id): object
    {
        if (!class_exists($id)) {
            throw new RuntimeException("Dependência não encontrada: {$id}");
        }

        $reflection = new ReflectionClass($id);
        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Dependência não pode ser instanciada: {$id}");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return $reflection->newInstance();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new RuntimeException("Não foi possível resolver {$id}::\${$parameter->getName()}.");
            }

            $arguments[] = $this->get($type->getName());
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
