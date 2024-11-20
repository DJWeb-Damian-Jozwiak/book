<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\TraceFrameFetcher;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\VariableFormatter;
use DJWeb\Framework\ErrorHandling\TraceCollection;
use DJWeb\Framework\ErrorHandling\TraceFrame;

readonly class FrameVariablesRenderer extends BaseFrameRenderer
{
    public function __construct(
        OutputContract $output,
        TraceFrameFetcher $frameFetcher,
        private VariableFormatter $varFormatter
    ) {
        parent::__construct($output, $frameFetcher);
    }

    public function render(TraceCollection $trace, int $frameIndex): void
    {
        $frame = $this->getFrame($trace, $frameIndex);
        if (! $frame) { return;
        }

        $this->output->info("Local variables in frame #{$frameIndex}:");
        $this->renderVariables($frame);
    }

    private function renderVariables(TraceFrame $frame): void
    {
        $this->output->writeln('$this = ' . $this->varFormatter->format($frame->args[0] ?? null));
    }
}
