<?php

namespace Tests;

use DJWeb\Framework\Application;
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
        $app = new Application();
        $response = $app->handle();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}