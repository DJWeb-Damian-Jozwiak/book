<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\Backtrace;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\DebugSession;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\ConsoleCommandProcessor;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\ConsoleHeaderRenderer;
use Throwable;

readonly class ConsoleRenderer
{
    public function __construct(
        private OutputContract $output,
        private ConsoleHeaderRenderer $headerRenderer,
        private ConsoleCommandProcessor $commandProcessor
    ) {
    }

    public function render(Throwable $exception): int
    {
        $debugSession = new DebugSession(
            $this->output,
            $this->commandProcessor,
            new Backtrace()->generate($exception)
        );

        $this->headerRenderer->render($exception);
        return $debugSession->run();
    }
}
