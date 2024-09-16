<?php

namespace Tests\Http;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\RequestFactory;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Router;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    private ContainerContract $container;
    private Router $router;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
        $this->container->set(ContainerContract::class, $this->container);
        $this->router = new Router($this->container);
        $this->router->addRoute(
            'GET',
            '/',
            fn() => (new Response())->withContent('hello world')
        );
        $this->container->set(Router::class, $this->router);
    }

    public function testHandleReturnsResponse()
    {
        $kernel = new Kernel($this->container);
        $request = (new RequestFactory())->createRequest('GET', '/');
        $response = $kernel->handle($request);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testHandleResponseContent()
    {
        $kernel = new Kernel($this->container);
        $request = (new RequestFactory())->createRequest('GET', '/');
        $response = $kernel->handle($request);
        $this->assertEquals('hello world', (string)$response->getBody());
    }
}