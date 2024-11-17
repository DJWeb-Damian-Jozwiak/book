<?php

namespace Tests\Http\Middleware;

use DJWeb\Framework\Http\Middleware\RequestLogger\ContextBuilder;
use DJWeb\Framework\Http\Middleware\RequestLoggerMiddleware;
use DJWeb\Framework\Http\Request\Psr7\Request;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Tests\BaseTestCase;

class LogMiddlewareTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Application::withInstance(null);

    }
    public function testException()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $exception = new RuntimeException('Test exception');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willThrowException($exception);

        $logger->expects($this->once())->method('error');
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__,2));
        $app->set(LoggerInterface::class, $logger);
        $middleware = new RequestLoggerMiddleware($logger, new ContextBuilder());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test exception');
        $middleware->process(
            $this->createMock(ServerRequestInterface::class),
            $handler
        );
    }

    public function testSuccess()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle');

        $logger->expects($this->once())->method('info');
        $app = Application::getInstance();
        $app->bind('base_path', dirname(__DIR__,2));
        $app->set(LoggerInterface::class, $logger);
        $middleware = new RequestLoggerMiddleware($logger, new ContextBuilder());
        $middleware->process(
            $this->createMock(ServerRequestInterface::class),
            $handler
        );
    }
}