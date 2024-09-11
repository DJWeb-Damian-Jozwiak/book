<?php

namespace Tests;

use DJWeb\Framework\Application;
use DJWeb\Framework\Exceptions\Container\ContainerException;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ApplicationTest extends TestCase
{
    public function testApplicationHandlesRequest(): void
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => 'http',
            'SERVER_PORT' => 80,
            'SERVER_NAME' => 'test.local'
        ];
        $app = Application::getInstance();
        $app->withRoutes(function (Router $router){
            $router->addRoute('GET', '/', function () {
                return (new Response())->setContent('Hello, World');
            });
        });
        $response = $app->handle();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testCannotClone()
    {
        $this->expectException(ContainerException::class);
        $app = Application::getInstance();
        $app2 = clone $app;
    }

    public function testCannotSerialize()
    {
        $this->expectException(ContainerException::class);
        $app = Application::getInstance();
        unserialize(serialize($app));
    }
}