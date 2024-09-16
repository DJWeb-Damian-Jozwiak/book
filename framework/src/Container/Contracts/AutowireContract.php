<?php

declare(strict_types=1);

namespace DJWeb\Framework\Container\Contracts;

use DJWeb\Framework\Exceptions\Container\NotFoundError;
use ReflectionException;

/**
 * Interface AutowireContract
 *
 * Defines the contract for the Autowire class.
 */
interface AutowireContract
{
    /**
     * Autowire and instantiate a class.
     *
     * @template T of object
     *
     * @param class-string<T> $className The name of the class to instantiate
     *
     * @return T The instantiated object
     *
     * @throws ReflectionException If the class cannot be reflected
     * @throws NotFoundError If a dependency cannot be resolved
     */
    public function instantiate(string $className): object;

    /**
     * Resolve parameters for dependency injection.
     *
     * @param array<\ReflectionParameter> $parameters
     *
     * @return array<mixed>
     *
     * @throws NotFoundError If a dependency cannot be resolved
     */
    public function resolveParameters(array $parameters): array;
}
