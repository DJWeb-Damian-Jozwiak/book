<?php

declare(strict_types=1);

namespace DJWeb\Framework\ServiceProviders;

use DJWeb\Framework\Console\Resolvers\CommandResolver;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\ServiceProvider;
use DJWeb\Framework\Http\Stream;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register(ContainerContract $container): void
    {
        $container->set(
            key: 'input_stream',
            value: new Stream('php://stdin'),
        );
        $container->set(
            key: 'output_stream',
            value: new Stream('php://stdout'),
        );
        $container->set(
            key: CommandResolver::class,
            value: new CommandResolver($container),
        );
    }
}
