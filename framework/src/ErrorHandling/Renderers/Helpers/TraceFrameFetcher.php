<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Helpers;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\TraceCollection;
use DJWeb\Framework\ErrorHandling\TraceFrame;

readonly class TraceFrameFetcher
{
    public function __construct(
        private OutputContract $output
    ) {
    }

    public function fetch(TraceCollection $trace, int $frameIndex): ?TraceFrame
    {
        $frame = $trace->frames[$frameIndex] ?? null;

        if (! $frame) {
            $this->output->error("Frame {$frameIndex} not found");
            return null;
        }

        return $frame;
    }
}
