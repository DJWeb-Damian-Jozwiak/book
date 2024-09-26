<?php

namespace Tests\Console\Commands;

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Commands\ListCommand;
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
        $output = $this->createMock(StreamInterface::class);
        $app->set('output_stream', $output);
        $command = new ListCommand($app);
        $this->assertEquals(0, $command->run());
    }
}