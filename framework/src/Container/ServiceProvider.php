<?php

declare(strict_types=1);

namespace DJWeb\Framework\Container;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\Contracts\ServiceProviderContract;

class ServiceProvider implements ServiceProviderContract
{
    /**
     * @var array<int, ServiceProvider>
     */
    private array $providers = [];

    /** @var array<int|string, Definition> */
    private array $definitions = [];

    /**
     * Add a service provider to the aggregate.
     *
     * @param ServiceProvider $provider The service provider to add
     *
     * @return self
     */
    public function addProvider(ServiceProvider $provider): self
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * Add a definition directly to the aggregate.
     *
     * @param Definition $definition The definition to add
     *
     * @return self
     */
    public function addDefinition(Definition $definition): self
    {
        $this->definitions[$definition->id] = $definition;
        return $this;
    }

    /**
     * Get all definitions from all service providers.
     *
     * @return array<int|string, Definition>
     */
    public function getDefinitions(): array
    {
        $allDefinitions = $this->definitions;
        foreach ($this->providers as $provider) {
            $allDefinitions = [...$allDefinitions, ...$provider->getDefinitions()];
        }
        return $allDefinitions;
    }

    /**
     * Register all service providers in the given container.
     *
     * @param ContainerContract $container The container to register services in
     *
     * @return void
     */
    public function register(ContainerContract $container): void
    {
        foreach ($this->getDefinitions() as $definition) {
            $container->addDefinition($definition);
        }
    }

    /**
     * Get all registered service providers.
     *
     * @return array<int, ServiceProvider>
     */
    public function getProviders(): array
    {
        return $this->providers;
    }
}
