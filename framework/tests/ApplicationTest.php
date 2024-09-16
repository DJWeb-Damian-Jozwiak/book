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
        $app = new Application();
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