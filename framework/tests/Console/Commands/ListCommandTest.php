<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Commands\ListCommand;
use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Console\Output\Implementation\ConsoleOutput;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ListCommandTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();
        Application::withInstance(null);
    }

    public function testListCommand()
    {
        $app = Application::getInstance();
        $outputStream = $this->createMock(StreamInterface::class);
        $app->set('output_stream', $outputStream);
        $command = new ListCommand($app);
        $output = new ConsoleOutput($app);
        $app->set(OutputContract::class, $output);
        $this->assertEquals(0, $command->run());
    }
}