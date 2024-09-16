<?php

namespace Tests\Console\Resolvers;

use DJWeb\Framework\Console\Resolvers\CommandResolver;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Console\CommandNotFound;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestCommand;

class CommandResolverTest extends TestCase
{
    private ContainerContract $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testResolveCommand()
    {
        new TestCommand($this->container);
        $resolver = new CommandResolver($this->container);
        $value = $resolver->resolve('test');
        $this->assertInstanceOf(TestCommand::class, $value);
    }

    public function testResolveCommandWithException()
    {
        $this->expectException(CommandNotFound::class);
        $resolver = new CommandResolver($this->container);
        $resolver->resolve('nonexistent');
    }
}