<?php

namespace Console\Resolvers;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Output\Implementation\ConsoleOutput;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\TestCommand;

class OptionsResolverTest extends TestCase
{
    private TestCommand $command;
    private Application $app;
    private $mockOutput;

    protected function setUp(): void
    {
        $this->app = Application::getInstance();
        $this->command = new TestCommand($this->app);
        $this->mockOutput = $this->createMock(ConsoleOutput::class);
        $this->command->withOutput($this->mockOutput);
    }

    public function testResolveDefaultOption()
    {
        $this->mockOutput->expects($this->once())
            ->method('question')
            ->with('Enter value for option required_option: ')
            ->willReturn('user_input');

        $this->command->resolveOptions(['option' => 'default_value']);

        $this->assertEquals('default_value', $this->command->getTestOption());
    }

    public function testResolveRequiredOption()
    {
        $this->mockOutput->expects($this->once())
            ->method('question')
            ->with('Enter value for option required_option: ')
            ->willReturn('user_input');

        $this->command->resolveOptions();

        $this->assertEquals('user_input', $this->command->getRequiredOption());
    }

    public function testResolveOptionsWithParameters()
    {
        $this->mockOutput->expects($this->never())->method('question');
        $this->command->resolveOptions(
            ['option' => 'test_value', 'required_option' => 'test_value']
        );
        $this->assertEquals('test_value', $this->command->getTestOption());
        $this->assertEquals('test_value', $this->command->getRequiredOption());
    }
}