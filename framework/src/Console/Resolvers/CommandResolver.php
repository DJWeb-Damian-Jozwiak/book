<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Resolvers;

use DJWeb\Framework\Console\Command;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Console\CommandNotFound;

class CommandResolver
{
    public function __construct(
        private readonly ContainerContract $container
    ) {
    }

    public function resolve(string $commandName): Command
    {
        if (! $this->container->has('command.' . $commandName)) {
            throw new CommandNotFound($commandName);
        }
        return $this->container->get('command.' . $commandName);
    }
}
