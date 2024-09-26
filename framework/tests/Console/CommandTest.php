<?php

namespace Tests\Console;

use DJWeb\Framework\Console\Application;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestCommand;

class CommandTest extends TestCase
{
    public function tearDown(): void
    {
        Application::withInstance(null);
    }

    private Application $container;

    protected function setUp(): void
    {
        $this->container = Application::getInstance();
    }

    public function testConstructorSetsContainer()
    {
        $command = new TestCommand($this->container);

        $this->assertSame($this->container, $command->container);
        $this->assertTrue($this->container->has('command.test'));
    }
}