<?php

namespace DJWeb\Framework\Container\Contracts;

interface DefinitionInterface
{
    /**
     * Add an argument to the definition.
     *
     * @param mixed $argument The argument to add.
     * @return self
     */
    public function addArgument(mixed $argument): self;

    /**
     * Add a method call to the definition.
     *
     * @param string $method The method name.
     * @param array<int, string> $arguments The arguments for the method call.
     * @return self
     */
    public function addMethodCall(string $method, array $arguments = []): self;

    /**
     * Get the arguments of the definition.
     *
     * @return array<int, string> The arguments.
     */
    public function getArguments(): array;

    /**
     * Get the method calls of the definition.
     *
     * @return array<int, array{string, array<int, mixed>}> The method calls.
     */
    public function getMethodCalls(): array;
}