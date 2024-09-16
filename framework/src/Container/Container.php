<?php

declare(strict_types=1);

namespace DJWeb\Framework\Container;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;
use DJWeb\Framework\Exceptions\Container\NotFoundError;

class Container implements ContainerContract
{
    /** @var array<string, mixed> */
    private array $entries = [];
    private Autowire $autowire;

    public function __construct()
    {
        $this->autowire = new Autowire($this);
    }

    /**
     * @param class-string $id
     *
     * @return mixed
     *
     * @throws NotFoundError
     */
    public function get(string $id): mixed
    {
        if (! $this->has($id)) {
            return $this->autowire->instantiate($id);
        }
        $entry = $this->entries[$id];
        if ($entry instanceof Definition) {
            return $this->autowire->instantiate($entry::class);
        }
        return $entry;
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * Sets item in container
     *
     * @param string $key
     * @param mixed $value
     *
     * @return ContainerContract
     */
    public function set(string $key, mixed $value): ContainerContract
    {
        $this->entries[$key] = $value;
        return $this;
    }

    /**
     * Add a definition to the container.
     *
     * @param Definition $definition
     *
     * @return self
     */
    public function addDefinition(Definition $definition): ContainerContract
    {
        $this->entries[$definition->id] = $definition;
        return $this;
    }

    /**
     * Register a service provider.
     *
     * @param ServiceProviderContract $provider
     *
     * @return self
     */
    public function register(ServiceProviderContract $provider): ContainerContract
    {
        $provider->register($this);
        return $this;
    }
}
