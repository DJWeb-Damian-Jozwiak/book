<?php

declare(strict_types=1);

namespace DJWeb\Framework\Container\Contracts;

use DJWeb\Framework\Container\Definition;

/**
 * The ContainerContract defines the methods that a service container must implement.
 * It extends the PSR-11 ContainerContract and provides additional methods for setting
 * services, adding definitions, and registering service providers.
 */
interface ContainerContract extends \Psr\Container\ContainerInterface
{
    public function set(string $key, mixed $value): ContainerContract;

    public function addDefinition(Definition $definition): ContainerContract;

    public function register(
        ServiceProviderContract $provider
    ): ContainerContract;

    public function bind(string $key, string|int|float|bool|null $value): void;

    public function getBinding(string $key): string|int|float|bool|null;
}
