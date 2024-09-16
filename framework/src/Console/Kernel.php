<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console;

use DJWeb\Framework\Console\Output\Implementation\ConsoleOutput;
use DJWeb\Framework\Console\Resolvers\CommandResolver;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Console\NoCommandSpecified;

readonly class Kernel
{
    public function __construct(
        private ContainerContract $container,
        private CommandResolver $commandResolver
    ) {
    }

    /**
     * @param array<int, string> $input
     *
     * @return int
     */
    public function handle(array $input = []): int
    {
        $commandName = $input[1] ?? throw new NoCommandSpecified();

        $command = $this->commandResolver->resolve($commandName);

        $inputValues = $this->parseInput(array_slice($input, 1));

        $command->withOutput(new ConsoleOutput($this->container));
        $command->resolveAttributes($inputValues);
        $command->resolveOptions($inputValues);

        return $command->run();
    }

    /**
     * @param array<int, string> $input
     *
     * @return array<string|int, mixed>
     */
    private function parseInput(array $input): array
    {
        $values = [];
        foreach ($input as $arg) {
            if (str_contains($arg, '=')) {
                [$key, $value] = explode('=', $arg, 2);
                $values[ltrim($key, '-')] = $value;
            } else {
                $values[] = $arg;
            }
        }
        return $values;
    }
}
