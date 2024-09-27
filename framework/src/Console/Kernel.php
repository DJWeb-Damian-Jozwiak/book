<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console;

use DJWeb\Framework\Console\Resolvers\CommandResolver;
use DJWeb\Framework\Exceptions\Console\NoCommandSpecified;

readonly class Kernel
{
    public function __construct(
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
        $commandName = $input[0] ?? throw new NoCommandSpecified();

        $command = $this->commandResolver->resolve($commandName);

        $inputValues = array_slice($input, 1);
        $arguments = $this->parseArguments($inputValues);
        $options = $this->parseOptions($inputValues);

        $command->resolveAttributes($arguments);
        $command->resolveOptions($options);

        return $command->run();
    }

    /**
     * @param array<int, string> $input
     *
     * @return array<string|int, mixed>
     */
    private function parseArguments(array $input): array
    {
        $values = array_filter(
            $input,
            static fn ($arg) => ! str_contains($arg, '=')
        );
        return array_filter(
            $values,
            static fn ($arg) => ! str_starts_with($arg, '-')
        );
    }

    /**
     * @param array<int, string> $input
     *
     * @return array<string|int, mixed>
     */
    private function parseOptions(array $input): array
    {
        $values = [];
        $items = array_filter(
            $input,
            static fn ($arg) => str_contains($arg, '=')
        );
        foreach ($items as $arg) {
            [$key, $value] = explode('=', $arg, 2);
            $values[ltrim($key, '-')] = $value;
        }
        return $values;
    }
}
