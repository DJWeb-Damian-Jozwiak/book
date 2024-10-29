<?php

namespace Tests\Log;

use Carbon\Carbon;
use DJWeb\Framework\Enums\Log\LogLevel;
use DJWeb\Framework\Log\Context;
use DJWeb\Framework\Log\Contracts\FormatterContract;
use DJWeb\Framework\Log\Handlers\FileHandler;
use DJWeb\Framework\Log\Message;
use DJWeb\Framework\Log\Rotators\DailyRotator;
use PHPUnit\Framework\TestCase;

class FileHandlerTest extends TestCase
{
    private string $logPath;
    private FormatterContract $formatter;
    private FileHandler $fileHandler;
    private DailyRotator $rotator;

    protected function setUp(): void
    {
        Carbon::setTestNow(Carbon::create(2023, 1, 1));
        $this->logPath = __DIR__ . '/test-2023-01-01.log';
        $this->formatter = $this->createMock(FormatterContract::class);
        $this->rotator = new DailyRotator();
        $this->fileHandler = new FileHandler($this->logPath, $this->formatter, $this->rotator);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }
        // Clean up rotated files
        $files = glob(dirname($this->logPath) . '/*.log'); // adjust the glob pattern based on your naming convention
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function testHandleWritesToLog(): void
    {
        $message = new Message(LogLevel::INFO, 'Test log entry.', new Context());
        $this->formatter->method('format')->willReturn('Formatted log entry.');

        $this->fileHandler->handle($message);

        $this->assertFileExists($this->logPath);
        $this->assertStringContainsString('Formatted log entry.', file_get_contents($this->logPath));
    }

    public function testRotateLogOnNewDay(): void
    {
        $this->formatter->method('format')->willReturn('Formatted log entry.');
        $this->logPath = __DIR__ . '/test-2023-01-02.log';
        // Create the log file with a timestamp from yesterday
        touch($this->logPath, strtotime('-1 day'));

        $message = new Message(LogLevel::INFO, 'Test log entry.', new Context());
        $this->assertFileExists($this->logPath);

        // Check for rotated log file
        $this->fileHandler->handle(new Message(LogLevel::INFO,'Second log entry.', new Context()));
        $rotatedFileName = sprintf('test-%s.log', Carbon::now()->toDateString());
        $this->assertFileExists(dirname($this->logPath) . '/' . $rotatedFileName);
    }

    public function testDoesNotRotateIfSameDay(): void
    {

        // Create the log file with a timestamp from today
        touch($this->logPath);

        $this->fileHandler->handle(new Message(LogLevel::INFO,'Log entry for today.', new Context()));

        // Ensure that no rotation has occurred
        $this->assertFileExists($this->logPath);
        $rotatedFiles = glob(dirname($this->logPath) . '/*.log'); // Assuming all log files have .log extension
        $this->assertCount(1, $rotatedFiles);
    }

    public function testHandleCreatesDirectoryIfNotExists(): void
    {
        $this->logPath = __DIR__ . '/nonexistent_directory/test.log';
        $this->fileHandler = new FileHandler($this->logPath, $this->formatter, $this->rotator);

        $this->formatter->method('format')->willReturn('Test log entry.');
        $this->fileHandler->handle(new Message(LogLevel::INFO,'Test log entry.', new Context()));

        $this->assertFileExists($this->logPath);
        unlink($this->logPath);
        rmdir(__DIR__ . '/nonexistent_directory');
    }
}