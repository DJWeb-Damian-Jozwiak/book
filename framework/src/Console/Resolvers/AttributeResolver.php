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
        /** @var array<string, CommandArgument> $attributes */
        $attributes = (new CommandPropertyResolver())
            ->findPropertiesWithAttribute($command, CommandArgument::class);
        $attributes = array_values($attributes);
        $key = 0;
        $total = count($attributes);
        foreach ($inputValues as $index => $value) {
            $attributes[$index]->value = $value;
            $key++;
            $value = $attributes[$index]->value;
            $name = $attributes[$index]->name;
            $reflection->getProperty($name)->setValue($command, $value);
        }
        self::resolveMissing($key, $total, $attributes, $command, $reflection);
    }

    /**
     * @param int $key
     * @param int|null $total
     * @param array<int, CommandArgument> $attributes
     * @param Command $command
     * @param \ReflectionClass<Command> $reflection
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public static function resolveMissing(
        int $key,
        ?int $total,
        array $attributes,
        Command $command,
        \ReflectionClass $reflection
    ): void {
        for ($i = $key; $i < $total; $i++) {
            $name = $attributes[$i]->name;
            $attributes[$i]->value = $command->getOutput()->question(
                "Enter value for {$name}: "
            );
            $reflection->getProperty($name)->setValue(
                $command,
                $attributes[$i]->value
            );
        }
    }
}
