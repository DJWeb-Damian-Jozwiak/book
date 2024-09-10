<?php

namespace Tests\Routing;

use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouteCollectionTest extends TestCase
{
    private RouteCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new RouteCollection();
    }

    public function testAddRoute(): void
    {
        $route = new Route('/test', 'GET', fn() => 'test');
        $this->collection->addRoute($route);

        $this->assertCount(1, $this->collection);
        $this->assertSame($route, $this->collection->getRoutes()[0]);
    }

    public function testAddNamedRoute(): void
    {
        $route = new Route('/test', 'GET', fn() => 'test', 'test_route');
        $this->collection->addRoute($route);

        $this->assertSame($route, $this->collection->getNamedRoute('test_route'));
    }

    public function testAddDuplicateNamedRoute(): void
    {
        $route1 = new Route('/test1', 'GET', fn() => 'test1', 'test_route');
        $route2 = new Route('/test2', 'GET', fn() => 'test2', 'test_route');

        $this->collection->addRoute($route1);

        $this->expectException(\InvalidArgumentException::class);
        $this->collection->addRoute($route2);
    }

    public function testFindRoute(): void
    {
        $route1 = new Route('/test1', 'GET', fn() => 'test1');
        $route2 = new Route('/test2', 'POST', fn() => 'test2');

        $this->collection->addRoute($route1);
        $this->collection->addRoute($route2);

        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/test2');
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('POST');

        $this->assertSame($route2, $this->collection->findRoute($request));
    }

    public function testIteration(): void
    {
        $route1 = new Route('/test1', 'GET', fn() => 'test1');
        $route2 = new Route('/test2', 'POST', fn() => 'test2');

        $this->collection->addRoute($route1);
        $this->collection->addRoute($route2);

        $routes = iterator_to_array($this->collection);

        $this->assertCount(2, $routes);
        $this->assertSame($route1, $routes[0]);
        $this->assertSame($route2, $routes[1]);
    }
}