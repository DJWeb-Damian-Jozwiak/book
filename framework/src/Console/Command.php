<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console;

use DJWeb\Framework\Console\Attributes\AsCommand;
use DJWeb\Framework\Console\Output\Implementation\ConsoleOutput;
use DJWeb\Framework\Console\Resolvers\AttributeResolver;
use DJWeb\Framework\Console\Resolvers\OptionsResolver;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use ReflectionClass;

abstract class Command
{
    private ConsoleOutput $output;

    public function __construct(
        public readonly ContainerContract $container
    ) {
        $this->registerInContainer();
    }

    public function getOutput(): ConsoleOutput
    {
        return $this->output;
    }

    /**
     * @param array<string|int, mixed> $inputValues
     *
     * @return void
     */
    public function resolveAttributes(array $inputValues = []): void
    {
        AttributeResolver::resolve($this, $inputValues);
    }

    /**
     * @param array<string|int, mixed> $inputValues
     *
     * @return void
     */
    public function resolveOptions(array $inputValues = []): void
    {
        OptionsResolver::resolve($this, $inputValues);
    }

    public function withOutput(ConsoleOutput $output): void
    {
        $this->output = $output;
    }

    abstract public function run(): int;

    private function registerInContainer(): void
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes(AsCommand::class);

        if ($attributes) {
            $commandAttribute = $attributes[0]->newInstance();
            $this->container->set('command.' . $commandAttribute->name, $this);
        }
    }
}
