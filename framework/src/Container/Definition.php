<?php

declare(strict_types=1);

namespace DJWeb\Framework\Container;

use DJWeb\Framework\Container\Contracts\DefinitionContract;

class Definition implements DefinitionContract
{
    /**
     * @var array<int, mixed>
     */
    private array $arguments = [];
    /**
     * @var array<int, array{0: string, 1: array<int, mixed>}>
     */
    private array $methodCalls = [];

    public function __construct(
        public readonly string $id,
        public readonly string $className,
    ) {
    }

    /**
     * Add a constructor argument.
     *
     * @param mixed $argument The argument to add
     *
     * @return self
     */
    public function addArgument(mixed $argument): self
    {
        $this->arguments[] = $argument;
        return $this;
    }

    /**
     * Add a method call to be made after instantiation.
     *
     * @param string $method The method name
     * @param array<int, mixed> $arguments The arguments for the method call
     *
     * @return self
     */
    public function addMethodCall(string $method, array $arguments = []): self
    {
        $this->methodCalls[] = [$method, $arguments];
        return $this;
    }

    /**
     * Get the constructor arguments.
     *
     * @return array<int, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the method calls to be made after instantiation.
     *
     * @return array<int, array{0: string, 1: array<int, mixed>}>
     */
    public function getMethodCalls(): array
    {
        return $this->methodCalls;
    }
}
