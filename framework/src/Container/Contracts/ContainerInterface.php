<?php

namespace DJWeb\Framework\Container\Contracts;

use DJWeb\Framework\Container\Definition;


/**
 * The ContainerInterface defines the methods that a service container must implement.
 * It extends the PSR-11 ContainerInterface and provides additional methods for setting
 * services, adding definitions, and registering service providers.
 */
interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    public function set(string $key, mixed $value): ContainerInterface;

    public function addDefinition(Definition $definition): ContainerInterface;

    public function register(ServiceProviderInterface $provider): ContainerInterface;
}