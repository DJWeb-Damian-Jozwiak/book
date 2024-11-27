<?php

namespace Tests\Routing;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Http\MiddlewareStack;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\RouteGroup;
use DJWeb\Framework\Routing\RouteHandler;
use DJWeb\Framework\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\BaseTestCase;
use Tests\Helpers\TestController;

class RouteGroupTest extends BaseTestCase
{
    private Router $router;
    private ContainerContract $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
        $this->router = new Router($this->container);
    }

    public function testDispatchToCallable(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $uri->method('getPath')->willReturn('/group1/nested/test');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

        $handler = new RouteHandler(callback: fn() => $response);
        $handler2 = new RouteHandler(controller: TestController::class, action: 'testMethod');
        $request->expects($this->once())->method('withAttribute')
            ->with('route_response')->willReturnSelf();
        $request->expects($this->once())->method('getAttribute')
            ->with('route_response')->willReturn($response);
        $this->router->group('group1', function (RouteGroup $group) use ($handler, $handler2) {
            $group->group('nested', function (RouteGroup $group) use ($handler, $handler2) {
                $group->addRoute(new Route('/test', 'GET', $handler));
                $group->addRoute(new Route('/test2', 'GET', $handler2));
            }, namespace: 'Tests\Routing');
        });

        $stack = new MiddlewareStack($this->router);

        $result = $stack->handle($request);

        $this->assertSame($response, $result);
    }

}