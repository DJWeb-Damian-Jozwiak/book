<?php

namespace Tests\Console\Resolvers;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Console\Output\Implementation\ConsoleOutput;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestCommand;

class AttributeResolverTest extends TestCase
{
    public function tearDown(): void
    {
        Application::withInstance(null);
    }

    private TestCommand $command;
    private Application $app;
    private $mockOutput;

    protected function setUp(): void
    {
        $this->app = Application::getInstance();
        $this->command = new TestCommand($this->app);
        $this->mockOutput = $this->createMock(ConsoleOutput::class);
        $this->app->set(OutputContract::class, $this->mockOutput);
        $this->command->withOutput($this->mockOutput);
    }

    public function testResolveAttributes()
    {
        $this->mockOutput->expects($this->never())->method('info');
        $this->mockOutput->expects($this->once())
            ->method('question')
            ->with('Enter value for testArgument: ')
            ->willReturn('test_value');
        $this->command->resolveAttributes();
        $this->assertEquals('test_value', $this->command->getTestArgument());
    }

    public function testResolveAttributesWithParameters()
    {
        $this->mockOutput->expects($this->never())->method('question');
        $this->command->resolveAttributes([0 => 'test_value']);
        $this->assertEquals('test_value', $this->command->getTestArgument());
    }

}