<?php

namespace Tests\Routing;

use DJWeb\Framework\Routing\Route;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouteTest extends TestCase
{
    public function testRouteMatches(): void
    {
        $route = new Route('/test', 'GET', function () {
        });

        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/test');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

        $this->assertTrue($route->matches($request));
    }

    public function testInvalidHandler(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Route('test', 'GET', []);
    }

    public function testRouteDoesNotMatchDifferentPath(): void
    {
        $route = new Route('/test', 'GET', function () {
        });

        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/different');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

        $this->assertFalse($route->matches($request));
    }

    public function testRouteDoesNotMatchDifferentMethod(): void
    {
        $route = new Route('/test', 'GET', function () {
        });

        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/test');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('POST');

        $this->assertFalse($route->matches($request));
    }

    public function testRouteExecution(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $route = new Route('/test', 'GET', function () use ($response) {
            return $response;
        });

        $request = $this->createMock(ServerRequestInterface::class);

        $this->assertSame($response, $route->execute($request));
    }

    public function testRouteGetters(): void
    {
        $route = new Route('/test', 'GET', function () {
        }, 'test_route');

        $this->assertSame('/test', $route->path);
        $this->assertSame('GET', $route->method);
        $this->assertSame('test_route', $route->name);
    }

}