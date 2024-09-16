<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Resolvers;

use DJWeb\Framework\Console\Attributes\CommandArgument;
use DJWeb\Framework\Console\Command;

class AttributeResolver
{
    /**
     * @param Command $command
     * @param array<string|int, mixed> $inputValues
     */
    public static function resolve(
        Command $command,
        array $inputValues = []
    ): void {
        $reflection = new \ReflectionClass($command);
        $attributes = (new CommandPropertyResolver())
            ->findPropertiesWithAttribute($command, CommandArgument::class);
        foreach ($attributes as $name => $instance) {
            if (array_key_exists($instance->name, $inputValues)) {
                $instance->value = $inputValues[$instance->name];
            } elseif ($instance->value === null) {
                $instance->value = $command->getOutput()->question(
                    "Enter value for {$instance->name}: "
                );
            }
            $value = $instance->value ?? $instance->default;
            $reflection->getProperty($name)->setValue($command, $value);
        }
    }
}
