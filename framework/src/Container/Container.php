<?php

namespace DJWeb\Framework\Container;

use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Container\Contracts\ServiceProviderInterface;
use DJWeb\Framework\Exceptions\Container\NotFoundException;

class Container implements ContainerInterface
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
     * @return mixed
     * @throws NotFoundException
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            return $this->autowire->instantiate($id);
        }
        $entry = $this->entries[$id];
        if ($entry instanceof Definition) {
            return $this->autowire->instantiate($entry::class);
        }
        return $entry;
    }


    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * Sets item in container
     * @param string $key
     * @param mixed $value
     * @return ContainerInterface
     */
    public function set(string $key, mixed $value): ContainerInterface
    {
        $this->entries[$key] = $value;
        return $this;
    }

    /**
     * Add a definition to the container.
     *
     * @param Definition $definition
     * @return self
     */
    public function addDefinition(Definition $definition): ContainerInterface
    {
        $this->entries[$definition->id] = $definition;
        return $this;
    }

    /**
     * Register a service provider.
     *
     * @param ServiceProviderInterface $provider
     * @return self
     */
    public function register(ServiceProviderInterface $provider): ContainerInterface
    {
        $provider->register($this);
        return $this;
    }

}