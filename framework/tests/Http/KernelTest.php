<?php

namespace Tests\Http;

use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\Request;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Routing\Router;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
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
    }

    public function testHandleReturnsResponse()
    {
        $kernel = new Kernel();
        $request = Request::createFromSuperglobals();
        $response = $kernel->handle($request);
        $this->assertInstanceOf(Response::class, $response);
    }
    public function testHandleResponseContent()
    {
        $kernel = new Kernel();
        $request = Request::createFromSuperglobals();
        $response = $kernel->handle($request);
        $this->assertEquals('Hello world from kernel', (string)$response->getBody());
    }
}