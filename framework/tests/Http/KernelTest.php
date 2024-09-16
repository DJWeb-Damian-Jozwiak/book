<?php

namespace Tests\Http;

use DJWeb\Framework\Container\Container;
use DJWeb\Framework\Container\Contracts\ContainerInterface;
use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\RequestFactory;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Router;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    private ContainerInterface $container;
    private Router $router;

    public function setUp(): void
    {
        parent::setUp();
        $_GET = ['key' => 'value'];
        $_POST = ['postKey' => 'postValue'];
        $_COOKIE = ['cookieName' => 'cookieValue'];
        $_FILES = ['fileField' => ['name' => 'test.txt']];
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => 'http',
            'SERVER_PORT' => 80,
            'SERVER_NAME' => 'test.local'
        ];
        $this->container = new Container();
        $this->container->set(ContainerInterface::class, $this->container);
        $this->router = new Router($this->container);
        $this->router->addRoute(
            'GET',
            '/',
            fn() => (new Response())->setContent('hello world')
        );
        $this->container->set(Router::class, $this->router);
    }

    public function testHandleReturnsResponse()
    {
        $kernel = new Kernel($this->container);
        $request = Request::createFromSuperglobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testHandleResponseContent()
    {
        $kernel = new Kernel($this->container);
        $request = Request::createFromSuperglobals();
        $response = $kernel->handle($request);
        $this->assertEquals('hello world', (string)$response->getBody());
    }
}