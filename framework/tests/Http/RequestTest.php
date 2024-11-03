<?php

declare(strict_types=1);

namespace Tests\Http;

use DJWeb\Framework\Http\HeaderManager;
use DJWeb\Framework\Http\Request\Psr7\BaseRequest;
use DJWeb\Framework\Http\UriManager;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class RequestTest extends TestCase
{
    private BaseRequest $request;
    private UriInterface $uri;
    private StreamInterface $body;
    private HeaderManager $headerManager;

    protected function setUp(): void
    {
        $this->uri = new UriManager('https://example.com/test');
        $this->body = $this->createMock(StreamInterface::class);
        $this->headerManager = new HeaderManager([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);
        $this->request = new BaseRequest(
            'GET',
            $this->uri,
            $this->body,
            $this->headerManager,
        );
    }

    public function testGetRequestTarget(): void
    {
        $this->assertEquals(
            '/test/',
            $this->request->getRequestTarget()
        );

        $request = new BaseRequest(
            'GET',
            new UriManager('http://example.com'),
            $this->body,
            new HeaderManager()
        );
        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function testWithRequestTarget(): void
    {
        $new = $this->request->withRequestTarget('/new-target');
        $this->assertNotSame($this->request, $new);
    }

    public function testGetMethod(): void
    {
        $this->assertEquals('GET', $this->request->getMethod());
    }

    public function testWithMethod(): void
    {
        $new = $this->request->withMethod('POST');
        $this->assertNotSame($this->request, $new);
        $this->assertEquals('POST', $new->getMethod());
        $this->assertEquals('GET', $this->request->getMethod());
    }

    public function testWithUri(): void
    {
        $newUri = new UriManager('https://example.org/new')->withPort(8080);
        $new = $this->request->withUri($newUri);
        $this->assertNotSame($this->request, $new);
        $this->assertSame($newUri, $new->getUri());
    }

    public function testWithUriUpdatesHost(): void
    {
        $newUri = new UriManager('https://newexample.com');
        $new = $this->request->withUri($newUri);
        $this->assertTrue($new->hasHeader('Host'));
        $this->assertEquals('newexample.com', $new->getHeaderLine('Host'));
    }

    public function testWithUriPreservesHost(): void
    {
        $this->request = $this->request->withHeader('Host', 'example.com');
        $newUri = new UriManager('https://newexample.com');
        $new = $this->request->withUri($newUri, true);
        $this->assertEquals('example.com', $new->getHeaderLine('Host'));
    }

    public function testGetProtocolVersion(): void
    {
        $this->assertEquals('1.1', $this->request->getProtocolVersion());
    }

    public function testWithProtocolVersion(): void
    {
        $new = $this->request->withProtocolVersion('2.0');
        $this->assertNotSame($this->request, $new);
        $this->assertEquals('2.0', $new->getProtocolVersion());
        $this->assertEquals('1.1', $this->request->getProtocolVersion());
    }

    public function testGetHeaders(): void
    {
        $expected = [
            'Content-Type' => ['application/json'],
            'Accept' => ['application/json'],
        ];
        $this->assertEquals($expected, $this->request->getHeaders());
    }

    public function testHasHeader(): void
    {
        $this->assertTrue($this->request->hasHeader('Content-Type'));
        $this->assertTrue($this->request->hasHeader('Accept'));
        $this->assertFalse($this->request->hasHeader('X-Custom'));
    }

    public function testGetHeader(): void
    {
        $this->assertEquals(['application/json'],
            $this->request->getHeader('Content-Type'));
        $this->assertEquals([], $this->request->getHeader('X-Custom'));
    }

    public function testGetHeaderLine(): void
    {
        $this->assertEquals(
            'application/json',
            $this->request->getHeaderLine('Content-Type')
        );
        $this->assertEquals('', $this->request->getHeaderLine('X-Custom'));
    }

    public function testWithHeader(): void
    {
        $new = $this->request->withHeader('X-Custom', 'value');
        $this->assertNotSame($this->request, $new);
        $this->assertTrue($new->hasHeader('X-Custom'));
        $this->assertEquals(['value'], $new->getHeader('X-Custom'));
        $this->assertFalse($this->request->hasHeader('X-Custom'));
    }

    public function testWithAddedHeader(): void
    {
        $new = $this->request->withAddedHeader('Accept', 'text/html');
        $this->assertNotSame($this->request, $new);
        $this->assertEquals(['application/json', 'text/html'],
            $new->getHeader('Accept'));
        $this->assertEquals(['application/json'],
            $this->request->getHeader('Accept'));
    }


    public function testWithoutHeader(): void
    {
        $new = $this->request->withoutHeader('Content-Type');
        $this->assertNotSame($this->request, $new);
        $this->assertFalse($new->hasHeader('Content-Type'));
        $this->assertTrue($this->request->hasHeader('Content-Type'));
    }

    public function testGetBody(): void
    {
        $this->assertSame($this->body, $this->request->getBody());
    }

    public function testWithBody(): void
    {
        $newBody = $this->createMock(StreamInterface::class);
        $new = $this->request->withBody($newBody);
        $this->assertNotSame($this->request, $new);
        $this->assertSame($newBody, $new->getBody());
        $this->assertSame($this->body, $this->request->getBody());
    }
}