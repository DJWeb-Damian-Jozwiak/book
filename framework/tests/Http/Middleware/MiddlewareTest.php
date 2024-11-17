<?php

namespace Tests\Http\Middleware;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Route;
use DJWeb\Framework\Routing\RouteHandler;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\Web\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\BaseTestCase;

class MiddlewareTest extends BaseTestCase
{
    private mixed $middlewareClass;

    public function setUp(): void
    {
        parent::setUp();
        $this->middlewareClass = new class implements MiddlewareInterface
        {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                $response = $handler->handle($request);
                return $response->withHeader('X-Middleware-Test', 'true');
            }
        };
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
    }

    public function testMiddlewareBefore(): void
    {
        $app = Application::getInstance();
        $response = new Response();

        $handler = new RouteHandler(callback: fn() => $response);
        $app->withRoutes(function (Router $router) use ($handler) {
            $router->addRoute(
                new Route(
                    '/',
                    'GET',
                    $handler
                )->withMiddlewareBefore($this->middlewareClass::class)
            );
        });
        $response = $app->handle();
        $this->assertEquals('true', $response->getHeaderLine('X-Middleware-Test'));
    }

    public function testMiddlewareAfter(): void
    {
        $app = Application::getInstance();
        $response = new Response();
        $handler = new RouteHandler(callback: fn() => $response);
        $app->withRoutes(function (Router $router) use ($handler) {
            $router->addRoute(
                new Route(
                    '/',
                    'GET',
                    $handler
                )->withMiddlewareAfter($this->middlewareClass::class)
            );
        });
        $response = $app->handle();
        $this->assertEquals('true', $response->getHeaderLine('X-Middleware-Test'));
    }

    public function testWithoutMiddleware(): void
    {
        $app = Application::getInstance();
        $response = new Response();
        $handler = new RouteHandler(callback: fn() => $response);
        $app->withRoutes(function (Router $router) use ($handler) {
            $router->addRoute(
                new Route(
                    '/',
                    'GET',
                    $handler
                )
                    ->withMiddlewareAfter($this->middlewareClass::class)
                    ->withoutMiddleware($this->middlewareClass::class)
            );
        });
        $response = $app->handle();
        $this->assertEquals('', $response->getHeaderLine('X-Middleware-Test'));
    }

    public function testInvalidMiddleware(): void
    {
        $app = Application::getInstance();
        $response = new Response();
        $handler = fn() => $response;
        $this->expectException(\InvalidArgumentException::class);
        $app->withRoutes(function (Router $router) use ($handler) {
            $router->addRoute(
                new Route(
                    '/',
                    'GET',
                    $handler
                )->withMiddlewareAfter('invalid')
            );
        });

    }
}