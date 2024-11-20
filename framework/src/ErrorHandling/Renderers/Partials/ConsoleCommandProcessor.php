<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Partials;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\TraceCollection;

readonly class ConsoleCommandProcessor
{
    public function __construct(
        private OutputContract $output,
        private ConsoleTraceRenderer $traceRenderer,
        private ConsoleEnvironmentRenderer $envRenderer,
        private ConsoleFrameRenderer $frameRenderer,
        private ConsoleHelpRenderer $helpRenderer
    ) {
    }

    public function process(string $command, TraceCollection $trace): void
    {
        match (true) {
            $command === 'help' => $this->helpRenderer->render(),
            $command === 'trace' => $this->traceRenderer->render($trace),
            $command === 'env' => $this->envRenderer->render(),
            str_starts_with($command, 'frame ') => $this->frameRenderer->renderFrame(
                $trace,
                (int) substr($command, 6)
            ),
            str_starts_with($command, 'vars ') => $this->frameRenderer->renderVariables(
                $trace,
                (int) substr($command, 5)
            ),
            str_starts_with($command, 'source ') => $this->frameRenderer->renderSource(
                $trace,
                (int) substr($command, 7)
            ),
            default => $this->output->error('Unknown command. Type "help" for available commands.')
        };
    }
}
