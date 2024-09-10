<?php

namespace Tests\Http;

use DJWeb\Framework\Http\Request;
use DJWeb\Framework\Http\Request\Headers;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class RequestTest extends TestCase
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
    public function testCreateFromSuperglobals()
    {
        $request = Request::createFromSuperglobals();
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals(['key' => 'value'], $request->getParams);
        $this->assertEquals(['postKey' => 'postValue'], $request->postParams);
        $this->assertEquals(['cookieName' => 'cookieValue'], $request->cookies);
        $this->assertEquals(['fileField' => ['name' => 'test.txt']],
            $request->files);
        $this->assertEquals($_SERVER, $request->server);
    }

    public function testWithRequestTarget()
    {
        $request = Request::createFromSuperglobals();
        $newRequest = $request->withRequestTarget('/new-target');
        $this->assertInstanceOf(RequestInterface::class, $newRequest);
        $this->assertNotSame($request, $newRequest);
        $this->assertEquals('/new-target', $newRequest->getRequestTarget());
    }


    public function testWithRequestTargetEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = Request::createFromSuperglobals();
        $request->withRequestTarget('');
    }

    public function testRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = Request::createFromSuperglobals();
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testInvalidRequestMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $_SERVER['REQUEST_METHOD'] = 'INVALID';
        $request = Request::createFromSuperglobals();
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testRequestUri()
    {
        $request = Request::createFromSuperglobals();
        $this->assertEquals('http://test.local:80?key=value', (string)$request->getUri());
    }

    public function testGetHeaders()
    {
        $headers = new Headers(['Content-Type' => ['application/json']]);
        $request = Request::createFromSuperglobals()->withHeaders($headers);
        $this->assertEquals(['Content-Type' => ['application/json']], $request->getHeaders());
    }

    public function testWithHeader()
    {
        $headers = Headers::empty();
        $request = Request::createFromSuperglobals()->withHeaders($headers);
        $newRequest = $request->withHeader('Content-Type', 'application/json');
        $this->assertNotSame($request, $newRequest);
        $this->assertEquals(['application/json'], $newRequest->getHeader('Content-Type'));
    }

    public function testWithAddedHeader()
    {
        $headers = new Headers(['Accept' => ['application/json']]);
        $request = Request::createFromSuperglobals()->withHeaders($headers);
        $newRequest = $request->withAddedHeader('Accept', 'text/html');
        $this->assertNotSame($request, $newRequest);
        $this->assertEquals(['application/json', 'text/html'], $newRequest->getHeader('Accept'));
    }

    public function testWithoutHeader()
    {
        $headers = new Headers(['Content-Type' => ['application/json'], 'HTTP_METHOD' => 'POST']);
        $request = Request::createFromSuperglobals()->withHeaders($headers);
        $newRequest = $request->withoutHeader('Content-Type');
        $this->assertNotSame($request, $newRequest);
        $this->assertFalse($newRequest->hasHeader('Content-Type'));
        $this->assertEquals(['POST'], $request->getHeader('Method'));
    }

    public function testHasHeader()
    {
        $headers = new Headers(['Content-Type' => ['application/json']]);
        $request = Request::createFromSuperglobals()->withHeaders($headers);
        $this->assertTrue($request->hasHeader('Content-Type'));
        $this->assertFalse($request->hasHeader('Authorization'));
    }

    public function testGetHeader()
    {
        $headers = new Headers(['Content-Type' => ['application/json']]);
        $request = Request::createFromSuperglobals()->withHeaders($headers);
        $this->assertEquals(['application/json'], $request->getHeader('Content-Type'));
    }
    public function testGetHeaderLine()
    {
        $headers = new Headers(['Content-Type' => ['application/json']]);
        $request = Request::createFromSuperglobals()->withHeaders($headers);
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
    }

    public function testGetProtocolVersion()
    {
        $request = Request::createFromSuperglobals();
        $this->assertEquals('1.1', $request->getProtocolVersion());
    }
    public function testWithProtocolVersion()
    {
        $request = Request::createFromSuperglobals();
        $newRequest = $request->withProtocolVersion('2.0');
        $this->assertNotSame($request, $newRequest);
        $this->assertEquals('2.0', $newRequest->getProtocolVersion());
    }

    public function testWithBody()
    {
        $stream = $this->createMock(StreamInterface::class);
        $request = Request::createFromSuperglobals();
        $newRequest = $request->withBody($stream);
        $this->assertNotSame($request, $newRequest);
        $this->assertSame($stream, $newRequest->getBody());
    }

    public function testGet()
    {
        $_GET = ['key' => 'value'];
        $request = Request::createFromSuperglobals();
        $this->assertEquals('value', $request->get('key'));
        $this->assertNull($request->get('nonexistent'));
        $this->assertEquals('default', $request->get('nonexistent', 'default'));
    }

    public function testPost()
    {
        $_POST = ['key' => 'value'];
        $request = Request::createFromSuperglobals();
        $this->assertEquals('value', $request->post('key'));
        $this->assertNull($request->post('nonexistent'));
        $this->assertEquals('default', $request->post('nonexistent', 'default'));
    }

    public function testJson()
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('{"key":"value"}');
        $request = Request::createFromSuperglobals()->withBody($stream);
        $this->assertEquals('value', $request->json('key'));
        $this->assertNull($request->json('nonexistent'));
        $this->assertEquals('default', $request->json('nonexistent', 'default'));
    }

    public function testJsonInvalid()
    {
        $this->expectException(\RuntimeException::class);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('invalid json');
        $request = Request::createFromSuperglobals()->withBody($stream);
        $request->json('key');
    }
}