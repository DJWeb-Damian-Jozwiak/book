<?php

namespace Tests\Http;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerContract;
use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\MiddlewareConfig;
use DJWeb\Framework\Http\Request\Psr17\RequestFactory;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\TestCase;
use Tests\BaseTestCase;

class KernelTest extends BaseTestCase
{
    private ContainerContract $container;
    private Router $router;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = Application::getInstance();
        $this->router = new Router($this->container);
        $this->router->addRoute(
            new Route('/', 'GET', fn() => new Response()->withContent('hello world') )
        );
        $this->container->set(Router::class, $this->router);
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())
            ->method('get')
            ->with('middleware')
            ->willReturn([
                'before_global' => [],
                'global' => [],
                'after_global' => [],
            ]);
        $this->container->set(ConfigContract::class, $config);
        $this->container->bind('base_path', dirname(__DIR__));
    }

    public function testHandleReturnsResponse()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $request = new RequestFactory()->createServerRequest('GET', '/');
        $response = $kernel->handle($request);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testHandleResponseContent()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $request = new RequestFactory()->createServerRequest('GET', '/');
        $response = $kernel->handle($request);
        $this->assertEquals('hello world', (string)$response->getBody());
    }
}