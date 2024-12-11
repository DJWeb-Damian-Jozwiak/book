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
use DJWeb\Framework\Routing\RouteHandler;
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
            new Route(
                '/',
                'GET',
                handler: new RouteHandler(callback: fn() => new Response()->withContent('hello world'))
            )
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

    public function testHandleRedirectsWithDefaultStatusCode()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $response = new Response(status: 302)
            ->withHeader('Location', '/new-location');

        $called = false;
        $actualStatusCode = null;

        $callback = function($statusCode) use (&$called, &$actualStatusCode) {
            $called = true;
            $actualStatusCode = $statusCode;
        };

        $kernel->handleRedirects($response, $callback);

        $this->assertTrue($called);
        $this->assertEquals(302, $actualStatusCode);
    }

    public function testHandleRedirectsPreservesValidStatusCodes()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $response = new Response(status: 301)
            ->withHeader('Location', '/permanent-redirect');

        $actualStatusCode = null;
        $callback = function($statusCode) use (&$actualStatusCode) {
            $actualStatusCode = $statusCode;
        };

        $kernel->handleRedirects($response, $callback);

        $this->assertEquals(301, $actualStatusCode);
    }

    public function testHandleRedirectsNormalizesInvalidStatusCodes()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $invalidStatusCodes = [200, 404, 500];

        foreach ($invalidStatusCodes as $statusCode) {
            $response = new Response(status: $statusCode)
                ->withHeader('Location', '/redirect');

            $actualStatusCode = null;
            $callback = function($statusCode) use (&$actualStatusCode) {
                $actualStatusCode = $statusCode;
            };

            $kernel->handleRedirects($response, $callback);

            $this->assertEquals(302, $actualStatusCode);
        }
    }

    public function testHandleRedirectsWithoutLocationHeader()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $response = new Response();

        $called = false;
        $callback = function() use (&$called) {
            $called = true;
        };

        $result = $kernel->handleRedirects($response, $callback);

        $this->assertFalse($called);
        $this->assertSame($response, $result);
    }

    public function testHandleResponseContent()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $request = new RequestFactory()->createServerRequest('GET', '/');
        $response = $kernel->handle($request);
        $this->assertEquals('hello world', (string)$response->getBody());
    }
    public function testHandleException()
    {
        $kernel = new Kernel($this->container, new MiddlewareConfig());
        $exception = new \Exception('error message');
        $request = new RequestFactory()->createServerRequest('GET', '/');
        $request = $request->withAttribute('exception', $exception);
        $this->expectException(\Exception::class);
        $kernel->handle($request);
    }
}