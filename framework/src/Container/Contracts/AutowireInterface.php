<?php

namespace DJWeb\Framework\Container\Contracts;

use DJWeb\Framework\Exceptions\Container\NotFoundException;
use ReflectionException;

/**
 * Interface AutowireInterface
 *
 * Defines the contract for the Autowire class.
 */
interface AutowireInterface
{
    /**
     * Autowire and instantiate a class.
     *
     * @template T of object
     * @param class-string<T> $className The name of the class to instantiate
     * @return T The instantiated object
     * @throws ReflectionException If the class cannot be reflected
     * @throws NotFoundException If a dependency cannot be resolved
     */
    public function instantiate(string $className): object;

    /**
     * Resolve parameters for dependency injection.
     *
     * @param array<\ReflectionParameter> $parameters
     * @return array<mixed>
     * @throws NotFoundException If a dependency cannot be resolved
     */
    public function resolveParameters(array $parameters): array;
}