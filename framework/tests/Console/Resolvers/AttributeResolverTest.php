<?php

namespace Tests\Console\Resolvers;

use DJWeb\Framework\Console\Output\Implementation\ConsoleOutput;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestCommand;

class AttributeResolverTest extends TestCase
{
    private TestCommand $command;
    private $mockContainer;
    private $mockOutput;

    protected function setUp(): void
    {
        $this->mockContainer = $this->createMock(ContainerContract::class);
        $this->command = new TestCommand($this->mockContainer);
        $this->mockOutput = $this->createMock(ConsoleOutput::class);
        $this->command->withOutput($this->mockOutput);
    }

    public function testResolveAttributes()
    {
        $this->mockOutput->expects($this->never())->method('info');
        $this->mockOutput->expects($this->once())
            ->method('question')
            ->with('Enter value for argument: ')
            ->willReturn('test_value');
        $this->command->resolveAttributes();
        $this->assertEquals('test_value', $this->command->getTestArgument());
    }

    public function testResolveAttributesWithParameters()
    {
        $this->mockOutput->expects($this->never())->method('question');
        $this->command->resolveAttributes(['argument' => 'test_value']);
        $this->assertEquals('test_value', $this->command->getTestArgument());
    }

}