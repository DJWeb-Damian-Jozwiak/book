<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Helpers;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\ConsoleCommandProcessor;
use DJWeb\Framework\ErrorHandling\TraceCollection;

class DebugSession
{
    private bool $isRunning = true;

    public function __construct(
        private readonly OutputContract $output,
        private readonly ConsoleCommandProcessor $processor,
        private readonly TraceCollection $trace
    ) {
    }

    public function run(): int
    {
        while ($this->isRunning) {
            $this->processNextCommand();
        }
        return 0;
    }

    private function processNextCommand(): void
    {
        $command = $this->output->question("Debug mode: (type 'help' for available commands) ");

        if ($command === 'exit') {
            $this->isRunning = false;
            return;
        }

        $this->processor->process($command, $this->trace);
    }
}
