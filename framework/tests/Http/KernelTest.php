<?php

namespace Tests\Http;

use DJWeb\Framework\Http\Kernel;
use DJWeb\Framework\Http\Request\Psr17\RequestFactory;
use DJWeb\Framework\Http\Response;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testHandleReturnsResponse()
    {
        $kernel = new Kernel();
        $request = new RequestFactory()->createServerRequest('GET', '/');
        $response = $kernel->handle($request);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testHandleResponseContent()
    {
        $kernel = new Kernel();
        $request = new RequestFactory()->createServerRequest('GET', '/');
        $response = $kernel->handle($request);
        $this->assertEquals(
            'Hello world from kernel',
            (string)$response->getBody()
        );
    }
}