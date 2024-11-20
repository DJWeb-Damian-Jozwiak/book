<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\TraceFrameFetcher;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\CodeSnippetRenderer;
use DJWeb\Framework\ErrorHandling\TraceCollection;

readonly class FrameSourceRenderer extends BaseFrameRenderer
{
    public function __construct(
        OutputContract $output,
        TraceFrameFetcher $frameFetcher,
        private CodeSnippetRenderer $snippetRenderer
    ) {
        parent::__construct($output, $frameFetcher);
    }

    public function render(TraceCollection $trace, int $frameIndex): void
    {
        $frame = $this->getFrame($trace, $frameIndex);
        if (! $frame) { return;
        }

        $this->output->info(sprintf(
            'Extended source for frame #%d (%s):',
            $frameIndex,
            basename($frame->file)
        ));

        $this->snippetRenderer->renderExtended($frame->file, $frame->line);
    }
}
