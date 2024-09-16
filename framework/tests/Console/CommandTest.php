<?php

namespace Tests\Console;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestCommand;

class CommandTest extends TestCase
{
    private ContainerContract $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testConstructorSetsContainer()
    {
        $command = new TestCommand($this->container);

        $this->assertSame($this->container, $command->container);
        $this->assertTrue($this->container->has('command.test'));
    }
}