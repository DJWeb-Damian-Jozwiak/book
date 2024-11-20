<?php

declare(strict_types=1);

namespace DJWeb\Framework\ErrorHandling\Renderers\Partials;

use DJWeb\Framework\Console\Output\Contacts\OutputContract;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\ErrorHandling\Renderers\ConsoleRenderer;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\PathNormalizer;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\TraceFrameFetcher;
use DJWeb\Framework\ErrorHandling\Renderers\Helpers\VariableFormatter;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame\FrameDetailsRenderer;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame\FrameSourceRenderer;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\Frame\FrameVariablesRenderer;

readonly class ConsoleRendererFactory
{
    public function __construct(
        private ContainerContract $container
    ) {
    }

    public function create(): ConsoleRenderer
    {
        return new ConsoleRenderer(
            $this->getOutput(),
            $this->createHeaderRenderer(),
            $this->createCommandProcessor()
        );
    }

    private function getOutput(): OutputContract
    {
        return $this->container->get(OutputContract::class);
    }

    private function createHeaderRenderer(): ConsoleHeaderRenderer
    {
        return new ConsoleHeaderRenderer(
            $this->getOutput()
        );
    }

    private function createTraceRenderer(): ConsoleTraceRenderer
    {
        return new ConsoleTraceRenderer(
            $this->getOutput(),
            $this->createPathNormalizer()
        );
    }

    private function createCommandProcessor(): ConsoleCommandProcessor
    {
        return new ConsoleCommandProcessor(
            $this->getOutput(),
            $this->createTraceRenderer(),
            $this->createEnvironmentRenderer(),
            $this->createFrameRenderer(),
            $this->createHelpRenderer()
        );
    }

    private function createEnvironmentRenderer(): ConsoleEnvironmentRenderer
    {
        return new ConsoleEnvironmentRenderer(
            $this->getOutput()
        );
    }

    private function createFrameRenderer(): ConsoleFrameRenderer
    {
        return new ConsoleFrameRenderer(
            $this->createFrameDetailsRenderer(),
            $this->createFrameVariablesRenderer(),
            $this->createFrameSourceRenderer()
        );
    }

    private function createFrameDetailsRenderer(): FrameDetailsRenderer
    {
        $frameFetcher = new TraceFrameFetcher($this->getOutput());
        return new FrameDetailsRenderer(
            $this->getOutput(),
            $frameFetcher,
            $this->createVariableFormatter(),
            $this->createSnippetRenderer()
        );
    }

    private function createFrameVariablesRenderer(): FrameVariablesRenderer
    {
        $frameFetcher = new TraceFrameFetcher($this->getOutput());
        return new FrameVariablesRenderer(
            $this->getOutput(),
            $frameFetcher,
            $this->createVariableFormatter()
        );
    }

    private function createFrameSourceRenderer(): FrameSourceRenderer
    {
        $frameFetcher = new TraceFrameFetcher($this->getOutput());
        return new FrameSourceRenderer(
            $this->getOutput(),
            $frameFetcher,
            $this->createSnippetRenderer()
        );
    }

    private function createHelpRenderer(): ConsoleHelpRenderer
    {
        return new ConsoleHelpRenderer(
            $this->getOutput()
        );
    }

    private function createSnippetRenderer(): CodeSnippetRenderer
    {
        return new CodeSnippetRenderer(
            $this->getOutput()
        );
    }

    private function createVariableFormatter(): VariableFormatter
    {
        return new VariableFormatter();
    }

    private function createPathNormalizer(): PathNormalizer
    {
        return new PathNormalizer();
    }
}
