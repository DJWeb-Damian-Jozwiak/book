<?php

namespace Tests\ErrorHandling;

use DJWeb\Framework\ErrorHandling\Handlers\ConsoleHandler;
use DJWeb\Framework\ErrorHandling\Renderers\ConsoleRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\BaseTestCase;

class ConsoleHandlerTest extends BaseTestCase
{
    private ConsoleRenderer|MockObject $renderer;
    private array $output;
    private ConsoleHandler $handler;

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(ConsoleRenderer::class);
        $this->output = [];
        $this->handler = new ConsoleHandler(
            $this->renderer,
            fn(string $content) => $this->output[] = $content
        );
    }

    public function testHandleException(): void
    {
        $exception = new \Exception('Test exception');

        $this->handler->handleException($exception);

        $this->assertNotEmpty($this->output);
    }

    public function testHandleExceptionWithRenderingFailure(): void
    {
        $exception = new \Exception('Test exception');

        $this->handler->handleException($exception);
        $errors = ['Critical error occurred. Please check error logs.'];
        $this->assertEquals($errors, $this->output);
    }

}