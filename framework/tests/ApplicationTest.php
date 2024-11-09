<?php

namespace Tests;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Exceptions\Container\ContainerError;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\RouteHandler;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ResponseInterface;

class ApplicationTest extends BaseTestCase
{
    public function testApplicationHandlesRequest(): void
    {
        $app = Application::getInstance();
        $app->withRoutes(function (Router $router) {
            $router->addRoute(new Route('/', 'GET', new RouteHandler(callback: fn() => new Response())));
        });
        $response = $app->handle();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testCannotClone()
    {
        $this->expectException(ContainerError::class);
        $app = Application::getInstance();
        $app2 = clone $app;
    }

    public function testCannotSerialize()
    {
        $this->expectException(ContainerError::class);
        $app = Application::getInstance();
        unserialize(serialize($app));
    }

    protected function setUp(): void
    {
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())
            ->method('get')
            ->with('middleware')
            ->willReturn([
                'before_global' => [],
                'global' => [],
                'after_global' => [],
            ]);
        $app->set(ConfigContract::class, $config);
        $app->bind('base_path', dirname(__DIR__));
    }
}