<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Handlers;

use DJWeb\Framework\Log\Contracts\FormatterContract;
use DJWeb\Framework\Log\Contracts\HandlerContract;
use DJWeb\Framework\Log\Contracts\RotatorContract;
use DJWeb\Framework\Log\Message;
use DJWeb\Framework\Log\Rotators\DailyRotator;

class FileHandler implements HandlerContract
{
    private readonly RotatorContract $rotator;
    public function __construct(
        private readonly string $logPath,
        private readonly FormatterContract $formatter,
        ?RotatorContract $rotator = null
    ) {
        $this->rotator = $rotator ?? new DailyRotator();

        $this->ensureDirectoryExists();
    }

    public function handle(Message $message): void
    {
        if ($this->rotator->shouldRotate($this->logPath)) {
            $this->rotator->rotate($this->logPath);
            $this->rotator->cleanup($this->logPath);
        }

        file_put_contents(
            $this->logPath,
            $this->formatter->format($message),
            FILE_APPEND | LOCK_EX
        );

        chmod($this->logPath, 0644);
    }

    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->logPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
