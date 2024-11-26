<?php

declare(strict_types=1);

namespace Tests\Http\Middleware;

use DJWeb\Framework\Http\Middleware\RedirectMiddleware;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RedirectMiddlewareTest extends TestCase
{
    private ?int $exitCode = null;
    private ServerRequestInterface $request;
    private RequestHandlerInterface $handler;
    private ResponseInterface $response;

    protected function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->handler->method('handle')
            ->with($this->request)
            ->willReturn($this->response);

    }

    public function testNoRedirectWhenLocationHeaderDoesNotExist(): void
    {
        $this->response->method('hasHeader')
            ->with('Location')
            ->willReturn(false);

        $middleware = new RedirectMiddleware(
            function (int $code) {
                $this->exitCode = $code;
            }
        );

        $response = $middleware->process($this->request, $this->handler);

        $this->assertNull($this->exitCode);
        $this->assertSame($this->response, $response);
    }

    public static function provideRedirectStatusCodes(): array
    {
        return [
            'redirect 301' => [301, 301],
            'redirect 302' => [302, 302],
            'redirect 303' => [303, 303],
            'redirect 307' => [307, 307],
            'redirect 308' => [308, 308],
            'non-redirect 200 becomes 302' => [200, 302],
            'non-redirect 201 becomes 302' => [201, 302],
            'error 404 becomes 302' => [404, 302],
            'error 500 becomes 302' => [500, 302],
        ];
    }

    #[DataProvider('provideRedirectStatusCodes')]
    public function testRedirectWithVariousStatusCodes(int $responseStatus, int $expectedRedirectStatus): void
    {
        $location = '/redirect-target';

        $this->response->method('hasHeader')->with('Location')->willReturn(true);

        $this->response->method('getHeaderLine')->with('Location')->willReturn($location);

        $this->response->method('getStatusCode')->willReturn($responseStatus);

        $middleware = new RedirectMiddleware(
            function (int $code) {
                $this->exitCode = $code;
            }
        );

        $response = $middleware->process($this->request, $this->handler);

        $this->assertEquals($expectedRedirectStatus, $this->exitCode);

        $this->assertSame($this->response, $response);
    }
}