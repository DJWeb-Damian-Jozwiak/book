<?php

declare(strict_types=1);

namespace DJWeb\Framework\Container;

use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Container\NotFoundError;
use ReflectionClass;
use ReflectionException;

/**
 * Class Autowire
 *
 * Responsible for automatically resolving and injecting dependencies.
 */
class Autowire
{
    private ReflectionResolver $resolver;

    public function __construct(
        private ContainerContract $container,
    ) {
        $this->resolver = new ReflectionResolver();
    }

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
    public function instantiate(string $className): object
    {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $parameters = $this->resolver->getConstructorParameters($className);
        $arguments = $this->resolveParameters($parameters);

        return $reflectionClass->newInstanceArgs($arguments);
    }

    /**
     * Resolve parameters for dependency injection.
     *
     * @param array<\ReflectionParameter> $parameters
     *
     * @return array<mixed>
     *
     * @throws NotFoundError If a dependency cannot be resolved
     */
    private function resolveParameters(array $parameters): array
    {
        return array_map(function (\ReflectionParameter $parameter) {
            $parameterName = $parameter->getName();
            $parameterType = $this->resolver->getParameterType($parameter);

            return match (true) {
                // 1. return given value if exists
                $this->container->has($parameterName) => $this->container->get($parameterName),
                $this->container->has($parameterType) => $this->container->get($parameterType),
                // 2. return default value if exist
                $this->resolver->hasDefaultValue($parameter) => $this->resolver->getDefaultValue($parameter),

                // 3. return null if allowed
                $this->resolver->allowsNull($parameter) => null,

                // 4. for builtin types return default value
                $parameterType && $this->isBuiltInType($parameterType) => $this->resolver
                    ->getDefaultValueForBuiltInType($parameterType),

                // 5. for object check recursively
                $parameterType && class_exists($parameterType) => $this->instantiate($parameterType),
                // otherwise throw not found exception
                default => throw new NotFoundError(
                    "Unable to resolve parameter {$parameterName} of type {$parameterType}"
                )
            };
        }, $parameters);
    }

    private function isBuiltInType(string $type): bool
    {
        return in_array($type, ['int', 'float', 'string', 'bool', 'array']);
    }
}
