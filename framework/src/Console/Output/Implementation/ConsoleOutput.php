<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Output\Implementation;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Console\Utils\Formatters\OutputFormatter;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use Psr\Http\Message\StreamInterface;

readonly class ConsoleOutput implements OutputContract
{
    protected OutputFormatter $formatter;
    private StreamInterface $inputStream;
    private StreamInterface $outputStream;

    public function __construct(private ContainerContract $container)
    {
        $this->formatter = new OutputFormatter($this->container);
        $this->inputStream = $this->container->get('input_stream');
        $this->outputStream = $this->container->get('output_stream');
    }

    public function write(string $message): void
    {
        $this->outputStream->write($this->formatter->format($message));
    }

    public function writeln(string $message): void
    {
        $this->write($message . PHP_EOL);
    }

    public function info(string $message): void
    {
        $this->writeln("<info>{$message}</info>");
    }

    public function warning(string $message): void
    {
        $this->writeln("<warning>{$message}</warning>");
    }

    public function error(string $message): void
    {
        $this->writeln("<danger>{$message}</danger>");
    }

    public function success(string $message): void
    {
        $this->writeln("<success>{$message}</success>");
    }

    public function question(string $text): string
    {
        $this->info($text);
        $this->outputStream->write(PHP_EOL);
        return trim($this->inputStream->read(1024));
    }
}
