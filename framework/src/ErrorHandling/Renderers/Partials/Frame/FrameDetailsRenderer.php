<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\TraceFrameFetcher;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\VariableFormatter;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\CodeSnippetRenderer;
use DJWeb\Framework\ErrorHandling\TraceCollection;
use DJWeb\Framework\ErrorHandling\TraceFrame;

readonly class FrameDetailsRenderer extends BaseFrameRenderer
{
    public function __construct(
        OutputContract $output,
        TraceFrameFetcher $frameFetcher,
        private VariableFormatter $varFormatter,
        private CodeSnippetRenderer $snippetRenderer
    ) {
        parent::__construct($output, $frameFetcher);
    }

    public function render(TraceCollection $trace, int $frameIndex): void
    {
        $frame = $this->getFrame($trace, $frameIndex);
        if (! $frame) { return;
        }

        $this->renderHeader($frame, $frameIndex);
        $this->renderCall($frame);
        $this->snippetRenderer->render($frame->snippet);
    }

    private function renderHeader(TraceFrame $frame, int $frameIndex): void
    {
        $this->output->writeln('');
        $this->output->info("Frame #{$frameIndex} Details:");
        $this->output->writeln(sprintf('Location: %s:%d', $frame->file, $frame->line));
    }

    private function renderCall(TraceFrame $frame): void
    {
        $this->output->writeln(sprintf(
            'Call: %s%s(%s)',
            $frame->class ? $frame->class . '::' : '',
            $frame->function,
            $this->varFormatter->formatArgs($frame->args)
        ));
    }
}
