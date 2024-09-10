<?php

namespace DJWeb\Framework\Container\Contracts;


use DJWeb\Framework\Container\Definition;

/**
 * Interface for service providers that can register multiple services at once.
 */
interface ServiceProviderInterface
{
    /**
     * Get the definitions for services provided by this service provider.
     *
     * @return Definition[]
     */
    public function getDefinitions(): array;

    /**
     * Register services in the given container.
     *
     * @param ContainerInterface $container The container to register services in
     * @return void
     */
    public function register(ContainerInterface $container): void;
}