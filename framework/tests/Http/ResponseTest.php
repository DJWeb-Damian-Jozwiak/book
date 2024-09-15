<?php

namespace Tests\Http;

use DJWeb\Framework\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testResponseStatusCode()
    {
        $response = new Response(200);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResponseReasonPhrase()
    {
        $response = new Response(200, 'OK');
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function testResponseWithStatus()
    {
        $response = new Response(200);
        $newResponse = $response->withStatus(404, 'Not Found');
        $this->assertEquals(404, $newResponse->getStatusCode());
        $this->assertEquals('Not Found', $newResponse->getReasonPhrase());
    }

    public function testResponseBody()
    {
        $response = new Response(200);
        $response->withContent('Hello World');
        $this->assertEquals('Hello World', (string)$response->getBody());
    }
}