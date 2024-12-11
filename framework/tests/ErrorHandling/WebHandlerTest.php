<?php

declare(strict_types=1);

namespace Tests\ErrorHandling;

use DJWeb\Framework\ErrorHandling\Handlers\WebHandler;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use ErrorException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class WebHandlerTest extends TestCase
{
    private WebRenderer|MockObject $renderer;
    private array $output;
    private WebHandler $handler;

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(WebRenderer::class);
        $this->output = [];
        $this->handler = new WebHandler(
            $this->renderer,
            fn(string $content) => $this->output[] = $content
        );
        $this->handler->register();
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

        $this->renderer->expects($this->once())
            ->method('render')
            ->with($exception)
            ->willThrowException(new \RuntimeException('Rendering failed'));

        $this->handler->handleException($exception);
        $errors = ['Critical error occurred. Please check error logs.'];
        $this->assertEquals($errors, $this->output);
    }

    public function testHandleFatalError(): void
    {
        trigger_error('Test user error2', E_USER_DEPRECATED);

        // Wywołujemy testowaną metodę
        $this->handler->handleFatalError();

        $this->assertNotEmpty($this->output);
    }

    public function testHandleError(): void
    {
        $fatalError = [
            'level' => E_ERROR,
            'message' => 'Fatal error',
            'file' => 'test.php',
            'line' => 123
        ];



       $this->expectException(ErrorException::class);

        $this->handler->handleError(...$fatalError);

    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->handler->unregister();
    }


    public function testHandleErrorWithSuppressedError(): void
    {
        // Temporarily change error_reporting
        $originalErrorReporting = error_reporting(0);

        $result = $this->handler->handleError(E_WARNING, 'Test error', 'test.php', 123);

        // Restore error_reporting
        error_reporting($originalErrorReporting);

        $this->assertFalse($result);
    }
}