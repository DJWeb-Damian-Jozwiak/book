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
        self::resolveProvided($command, $inputValues);
        self::resolveNotProvided($command, $inputValues);
    }

    /**
     * @param Command $command
     * @param array<string|int, mixed> $inputValues
     */
    public static function resolveProvided(
        Command $command,
        array $inputValues = []
    ): void {
        $reflection = new \ReflectionClass($command);
        /** @var array<string, CommandOption> $attributes */
        $attributes = (new CommandPropertyResolver())
            ->findPropertiesWithAttribute($command, CommandOption::class);

        // Filtruj atrybuty, dla których istnieje wartość w $inputValues
        $providedAttributes = array_filter(
            $attributes,
            static function ($instance) use ($inputValues) {
                return array_key_exists($instance->name, $inputValues);
            }
        );

        foreach ($providedAttributes as $name => $instance) {
            $instance->value = $inputValues[$instance->name];
            $value = $instance->value ?? $instance->default;
            $reflection->getProperty($name)->setValue($command, $value);
        }
    }

    /**
     * @param Command $command
     * @param array<string|int, mixed> $inputValues
     */
    public static function resolveNotProvided(
        Command $command,
        array $inputValues = []
    ): void {
        $reflection = new \ReflectionClass($command);
        /** @var array<string, CommandOption> $attributes */
        $attributes = (new CommandPropertyResolver())
            ->findPropertiesWithAttribute($command, CommandOption::class);

        $notProvidedAttributes = array_filter(
            $attributes,
            static function ($instance) use ($inputValues) {
                return ! array_key_exists(
                    $instance->name,
                    $inputValues
                ) && $instance->required && $instance->value === null;
            }
        );

        foreach ($notProvidedAttributes as $name => $instance) {
            $instance->value = $command->getOutput()->question(
                "Enter value for option {$instance->name}: "
            );
            $value = $instance->value ?? $instance->default;
            $reflection->getProperty($name)->setValue($command, $value);
        }
    }
}
