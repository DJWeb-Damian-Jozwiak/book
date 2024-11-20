<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Partials;

use DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame\FrameDetailsRenderer;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame\FrameSourceRenderer;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame\FrameVariablesRenderer;
use DJWeb\Framework\ErrorHandling\TraceCollection;

readonly class ConsoleFrameRenderer
{
    public function __construct(
        private FrameDetailsRenderer $detailsRenderer,
        private FrameVariablesRenderer $variablesRenderer,
        private FrameSourceRenderer $sourceRenderer
    ) {
    }

    public function renderFrame(TraceCollection $trace, int $frameIndex): void
    {
        $this->detailsRenderer->render($trace, $frameIndex);
    }

    public function renderVariables(TraceCollection $trace, int $frameIndex): void
    {
        $this->variablesRenderer->render($trace, $frameIndex);
    }

    public function renderSource(TraceCollection $trace, int $frameIndex): void
    {
        $this->sourceRenderer->render($trace, $frameIndex);
    }
}
