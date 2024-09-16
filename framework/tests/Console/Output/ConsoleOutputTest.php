<?php

namespace Console\Output;

use DJWeb\Framework\Console\Output\Implementation\ConsoleOutput;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ConsoleOutputTest extends TestCase
{
    private ContainerContract $container;
    private StreamInterface $inputStream;
    private StreamInterface $outputStream;
    private ConsoleOutput $consoleOutput;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerContract::class);
        $this->inputStream = $this->createMock(StreamInterface::class);
        $this->outputStream = $this->createMock(StreamInterface::class);

        $this->container->method('get')
            ->willReturnMap([
                ['input_stream', $this->inputStream],
                ['output_stream', $this->outputStream],
            ]);

        $this->consoleOutput = new ConsoleOutput($this->container);
    }

    public function testWrite(): void
    {
        $this->outputStream->expects($this->once())
            ->method('write')
            ->with($this->equalTo('Test message'));

        $this->consoleOutput->write('Test message');
        //$this->assertEquals($text, $result);
    }

    public function testWriteln(): void
    {
        $this->outputStream->expects($this->once())
            ->method('write')
            ->with($this->equalTo("Test message" . PHP_EOL));

        $this->consoleOutput->writeln('Test message');
    }

    public function testInfo(): void
    {
        $this->outputStream->expects($this->once())
            ->method('write')
            ->with($this->equalTo("\033[1;97mTest info\033[0m" . PHP_EOL));

        $this->consoleOutput->info('Test info');
    }

    public function testError(): void
    {
        $this->outputStream->expects($this->once())
            ->method('write')
            ->with($this->equalTo("\033[1;97;41mTest error\033[0m" . PHP_EOL));

        $this->consoleOutput->error('Test error');
    }

    public function testWarning(): void
    {
        $this->outputStream->expects($this->once())
            ->method('write')
            ->with(
                $this->equalTo("\033[1;30;43mTest warning\033[0m" . PHP_EOL)
            );

        $this->consoleOutput->warning('Test warning');
    }

    public function testSuccess(): void
    {
        $this->outputStream->expects($this->once())
            ->method('write')
            ->with(
                $this->equalTo("\033[1;97;42mTest warning\033[0m" . PHP_EOL)
            );

        $this->consoleOutput->success('Test warning');
    }

    public function testQuestion(): void
    {
        $this->outputStream->expects($this->exactly(2))
            ->method('write')
            ->willReturnCallback(function ($arg) {
                static $callNumber = 0;
                $callNumber++;

                if ($callNumber === 1) {
                    $this->assertEquals(
                        "\033[1;97mTest question\033[0m" . PHP_EOL,
                        $arg
                    );
                } elseif ($callNumber === 2) {
                    $this->assertEquals(PHP_EOL, $arg);
                }

                return strlen($arg);
            });

        $this->inputStream->expects($this->once())
            ->method('read')
            ->with($this->equalTo(1024))
            ->willReturn("Test answer");

        $answer = $this->consoleOutput->question('Test question');
        $this->assertEquals('Test answer', $answer);
    }
}