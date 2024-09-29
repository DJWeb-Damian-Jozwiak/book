<?php

namespace Tests\Console;

use DJWeb\Framework\Console\Command;
use DJWeb\Framework\Console\Kernel;
use DJWeb\Framework\Console\Resolvers\CommandResolver;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class KernelTest extends TestCase
{
    private ContainerContract $container;
    private CommandResolver $resolver;
    private Kernel $kernel;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->resolver = $this->createMock(CommandResolver::class);
        $this->kernel = new Kernel($this->resolver);
        $inputStream = $this->createMock(StreamInterface::class);
        $outputStream = $this->createMock(StreamInterface::class);
        $this->container->set('input_stream', $inputStream);
        $this->container->set('output_stream', $outputStream);
    }

    public function testHandleWithValidCommand()
    {
        $mockCommand = $this->createMock(Command::class);
        $mockCommand->expects($this->once())
            ->method('run')
            ->willReturn(0);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with('test')
            ->willReturn($mockCommand);

        $exitCode = $this->kernel->handle(
            ['test', '--option=value']
        );

        $this->assertEquals(0, $exitCode);
    }
}
