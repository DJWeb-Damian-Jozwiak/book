<?php

declare(strict_types=1);

namespace Tests\Routing;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Exceptions\Routing\RouteNotFoundError;
use DJWeb\Framework\Http\MiddlewareStack;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\RouteHandler;
use DJWeb\Framework\Routing\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\Helpers\TestController;

class RouterTest extends TestCase
{
    private Router $router;
    private ContainerContract $container;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->router = new Router($this->container);
    }

    public function testAddRoute(): void
    {
        $handler = new RouteHandler(callback: fn() => 'test');
        $this->router->addRoute(new Route('/test', 'GET', $handler));

        $this->expectNotToPerformAssertions();
    }

    public function testDispatchToCallable(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $uri->method('getPath')->willReturn('/test');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');
        $request->expects($this->once())->method('withAttribute')
            ->with('route_response')->willReturnSelf();
        $request->expects($this->once())->method('getAttribute')
            ->with('route_response')->willReturn($response);
        $handler = new RouteHandler(callback: fn() => $response);
        $this->router->addRoute(new Route('/test', 'GET', $handler));

        $stack = new MiddlewareStack($this->router);
        $result = $stack->handle($request);

        $this->assertSame($response, $result);
    }

    public function testDispatchToController(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/test');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');


        $this->router->addRoute(new Route(
                '/test',
                'GET',
                new RouteHandler(TestController::class, 'testMethod'))
        );

        $request->expects($this->once())->method('withAttribute')
            ->with('route_response')->willReturnSelf();
        $request->expects($this->once())->method('getAttribute')
            ->with('route_response')->willReturn(new Response()->withContent('ok'));
        $stack = new MiddlewareStack($this->router);
        $result = $stack->handle($request);
        $this->assertEquals(200, $result->getStatusCode());

        $this->assertEquals('ok', $result->getBody()->getContents());
    }

    public function testDispatchMissingRoute()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->expectException(RouteNotFoundError::class);
        $this->router->dispatch($request, $this->createMock(RequestHandlerInterface::class));
    }
}
