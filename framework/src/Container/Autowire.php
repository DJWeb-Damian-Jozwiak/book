<?php

namespace DJWeb\Framework\Container;

use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Exceptions\Container\NotFoundException;
use ReflectionClass;
use ReflectionException;

/**
 * Class Autowire
 *
 * Responsible for automatically resolving and injecting dependencies.
 */
class Autowire
{
    private ReflectionResolver $reflectionResolver;

    public function __construct(
        private ContainerInterface $container,
    ) {
        $this->reflectionResolver = new ReflectionResolver();
    }

    /**
     * Autowire and instantiate a class.
     *
     * @template T of object
     * @param class-string<T> $className The name of the class to instantiate
     * @return T The instantiated object
     * @throws ReflectionException If the class cannot be reflected
     * @throws NotFoundException If a dependency cannot be resolved
     */
    public function instantiate(string $className): object
    {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $parameters = $this->reflectionResolver->getConstructorParameters($className);
        $arguments = $this->resolveParameters($parameters);

        return $reflectionClass->newInstanceArgs($arguments);
    }

    /**
     * Resolve parameters for dependency injection.
     *
     * @param array<\ReflectionParameter> $parameters
     * @return array<mixed>
     * @throws NotFoundException If a dependency cannot be resolved
     */
    private function resolveParameters(array $parameters): array
    {
        return array_map(function (\ReflectionParameter $parameter) {
            $parameterName = $parameter->getName();
            $parameterType = $this->reflectionResolver->getParameterType($parameter);

            return match (true) {
                // 1. return given value if exists
                $this->container->has($parameterName) => $this->container->get($parameterName),

                // 2. return default value if exist
                $this->reflectionResolver->hasDefaultValue($parameter) => $this->reflectionResolver->getDefaultValue(
                    $parameter
                ),

                // 3. return null if allowed
                $this->reflectionResolver->allowsNull($parameter) => null,

                // 4. for builtin types return default value
                $parameterType && in_array($parameterType, ['int', 'float', 'string', 'bool', 'array'])
                => $this->reflectionResolver->getDefaultValueForBuiltInType($parameterType),

                // 5. for object check recursively
                $parameterType && class_exists($parameterType) => $this->instantiate($parameterType),
                // otherwise throw not found exception
                default => throw new NotFoundException(
                    "Unable to resolve parameter {$parameterName} of type {$parameterType}"
                )
            };
        }, $parameters);
    }
}