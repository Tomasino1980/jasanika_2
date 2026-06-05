<?php

declare(strict_types=1);

namespace Jasanika\Container;

final class Container
{
    /**
     * @var array<string, callable>
     */
    private array $factories = [];

    /**
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Register a service with a factory callable.
     *
     * @param string   $id      Fully qualified class name (ClassName::class).
     * @param callable $factory Factory that returns the service instance.
     */
    public function register(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    /**
     * Resolve a registered service.
     *
     * The same instance is returned on every call (singleton behavior).
     *
     * @param string $id Fully qualified class name.
     * @return object
     */
    public function get(string $id): object
    {
        if (!isset($this->instances[$id])) {
            $factory = $this->factories[$id];
            $this->instances[$id] = $factory($this);
        }

        return $this->instances[$id];
    }

    /**
     * Check whether a service is registered.
     *
     * @param string $id Fully qualified class name.
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }
}
