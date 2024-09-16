<?php

declare(strict_types=1);

namespace DJWeb\Framework\Container\Contracts;

use DJWeb\Framework\Container\Definition;

/**
 * Interface for service providers that can register multiple services at once.
 */
interface ServiceProviderContract
{
    /**
     * Get the definitions for services provided by this service provider.
     *
     * @return array<int, Definition>
     */
    public function getDefinitions(): array;

    /**
     * Register services in the given container.
     *
     * @param ContainerContract $container The container to register services in
     *
     * @return void
     */
    public function register(ContainerContract $container): void;
}
