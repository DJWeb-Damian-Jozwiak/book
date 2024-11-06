<?php

namespace Tests\Http\Middleware;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Http\Middleware\RouterMiddleware;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\Web\Application;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\BaseTestCase;

class RouterMiddlewareTest extends BaseTestCase
{
    public function setUp(): void
    {
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())
            ->method('get')
            ->with('middleware')
            ->willReturn([
                'before_global' => [],
                'global' => [
                    RouterMiddleware::class
                ],
                'after_global' => [],
            ]);
        $app->set(ConfigContract::class, $config);
    }

    public function testMiddleware()
    {
        $app = Application::getInstance();
        $response = new Response();
        $handler = fn() => $response;
        $app->withRoutes(function (Router $router) use ($handler) {
            $router->addRoute(
                new Route(
                    '/',
                    'GET',
                    $handler
                )
            );
        });
        $response = $app->handle();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testException()
    {
        $app = Application::getInstance();
        $response = new Response();
        $handler = fn() => $response;
        $app->withRoutes(function (Router $router) use ($handler) {
            $router->addRoute(
                new Route(
                    '/',
                    'GET',
                    $handler
                )
            );
        });
        $response = $app->handle();
        $this->assertInstanceOf(Response::class, $response);
    }
}