<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Resolvers;

use DJWeb\Framework\Console\Attributes\CommandOption;
use DJWeb\Framework\Console\Command;

class OptionsResolver
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
            ->findPropertiesWithAttribute($command, CommandOption::class);
        foreach ($attributes as $name => $instance) {
            if (array_key_exists($instance->name, $inputValues)) {
                $instance->value = $inputValues[$instance->name];
            } elseif ($instance->required && $instance->value === null) {
                $instance->value = $command->getOutput()->question(
                    "Enter value for option {$instance->name}: "
                );
            }
            $value = $instance->value ?? $instance->default;
            $reflection->getProperty($name)->setValue($command, $value);
        }
    }
}
