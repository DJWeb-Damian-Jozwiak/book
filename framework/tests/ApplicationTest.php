<?php

namespace Tests;

use DJWeb\Framework\Exceptions\Container\ContainerError;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Router;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ApplicationTest extends TestCase
{
    public function tearDown(): void
    {
        Application::withInstance(null);
    }
    public function testApplicationHandlesRequest(): void
    {
        $app = Application::getInstance();
        $app->withRoutes(function (Router $router) {
            $router->addRoute('GET', '/', fn() => new Response());
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
}