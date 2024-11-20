<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\TraceFrameFetcher;
use DJWeb\Framework\ErrorHandling\TraceCollection;
use DJWeb\Framework\ErrorHandling\TraceFrame;

abstract readonly class BaseFrameRenderer
{
    public function __construct(
        protected OutputContract $output,
        private TraceFrameFetcher $frameFetcher
    ) {
    }

    protected function getFrame(TraceCollection $trace, int $frameIndex): ?TraceFrame
    {
        return $this->frameFetcher->fetch($trace, $frameIndex);
    }
}
