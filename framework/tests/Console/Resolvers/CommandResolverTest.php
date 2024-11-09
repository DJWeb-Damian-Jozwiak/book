<?php

namespace Tests\Console\Resolvers;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Resolvers\CommandResolver;
use DJWeb\Framework\Exceptions\Console\CommandNotFound;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestCommand;

class CommandResolverTest extends TestCase
{
    private Application $app;

    public function setUp(): void
    {
          $this->app = Application::getInstance();
    }

    public function testResolveCommand()
    {
        new TestCommand($this->app);
        $resolver = new CommandResolver($this->app);
        $value = $resolver->resolve('test');
        $this->assertInstanceOf(TestCommand::class, $value);
    }

    public function testResolveCommandWithException()
    {
        $this->expectException(CommandNotFound::class);
        $resolver = new CommandResolver($this->app);
        $resolver->resolve('nonexistent');
    }
}