<?php

namespace Tests\Http;

use DJWeb\Framework\Http\HeaderManager;
use PHPUnit\Framework\TestCase;

class HeaderManagerTest extends TestCase
{
    private HeaderManager $headerManager;

    protected function setUp(): void
    {
        $this->headerManager = new HeaderManager([
            'Content-Type' => 'application/json',
            'Accept' => ['text/html', 'application/xhtml+xml']
        ]);
    }

    public function testGetHeaders(): void
    {
        $expected = [
            'Content-Type' => ['application/json'],
            'Accept' => ['text/html', 'application/xhtml+xml']
        ];
        $this->assertEquals($expected, $this->headerManager->getHeaders());
    }

    public function testHasHeader(): void
    {
        $this->assertTrue($this->headerManager->hasHeader('Content-Type'));
        $this->assertTrue($this->headerManager->hasHeader('Accept'));
        $this->assertFalse($this->headerManager->hasHeader('X-Custom-Header'));
    }

    public function testGetHeader(): void
    {
        $this->assertEquals(['application/json'],
            $this->headerManager->getHeader('Content-Type'));
        $this->assertEquals(['text/html', 'application/xhtml+xml'],
            $this->headerManager->getHeader('Accept'));
        $this->assertEquals([],
            $this->headerManager->getHeader('X-Custom-Header'));
    }

    public function testGetHeaderLine(): void
    {
        $this->assertEquals(
            'application/json',
            $this->headerManager->getHeaderLine('Content-Type')
        );
        $this->assertEquals(
            'text/html, application/xhtml+xml',
            $this->headerManager->getHeaderLine('Accept')
        );
        $this->assertEquals(
            '',
            $this->headerManager->getHeaderLine('X-Custom-Header')
        );
    }

    public function testWithHeader(): void
    {
        $new = $this->headerManager->withHeader(
            'X-Custom-Header',
            'custom-value'
        );

        $this->assertNotSame($this->headerManager, $new);
        $this->assertTrue($new->hasHeader('X-Custom-Header'));
        $this->assertEquals(['custom-value'],
            $new->getHeader('X-Custom-Header'));
    }

    public function testWithHeaderReplacesExistingHeader(): void
    {
        $new = $this->headerManager->withHeader('Content-Type', 'text/plain');

        $this->assertEquals(['text/plain'], $new->getHeader('Content-Type'));

        // Check that the original instance is not modified
        $this->assertEquals(['application/json'],
            $this->headerManager->getHeader('Content-Type'));
    }

    public function testWithAddedHeader(): void
    {
        $new = $this->headerManager->withAddedHeader(
            'Accept',
            'application/xml'
        );

        $this->assertNotSame($this->headerManager, $new);
        $this->assertEquals(
            ['text/html', 'application/xhtml+xml', 'application/xml'],
            $new->getHeader('Accept')
        );

        // Check that the original instance is not modified
        $this->assertEquals(['text/html', 'application/xhtml+xml'],
            $this->headerManager->getHeader('Accept'));
    }

    public function testWithAddedHeaderCreatesNewHeaderIfNotExists(): void
    {
        $new = $this->headerManager->withAddedHeader(
            'X-Custom-Header',
            'custom-value'
        );

        $this->assertTrue($new->hasHeader('X-Custom-Header'));
        $this->assertEquals(['custom-value'],
            $new->getHeader('X-Custom-Header'));
    }

    public function testWithoutHeader(): void
    {
        $new = $this->headerManager->withoutHeader('Content-Type');

        $this->assertNotSame($this->headerManager, $new);
        $this->assertFalse($new->hasHeader('Content-Type'));

        // Check that the original instance is not modified
        $this->assertTrue($this->headerManager->hasHeader('Content-Type'));
    }

    public function testWithoutHeaderIsIdempotent(): void
    {
        $new1 = $this->headerManager->withoutHeader('X-Custom-Header');
        $new2 = $new1->withoutHeader('X-Custom-Header');

        $this->assertEquals($new1->getHeaders(), $new2->getHeaders());
    }

    public function testHeaderNamesCaseInsensitive(): void
    {
        $this->assertTrue($this->headerManager->hasHeader('Content-Type'));
        $this->assertEquals(['application/json'],
            $this->headerManager->getHeader('Content-Type'));
    }

    public function testMultipleHeaderValues(): void
    {
        $headerManager = new HeaderManager([
            'Set-Cookie' => ['cookie1=value1', 'cookie2=value2']
        ]);

        $this->assertEquals(['cookie1=value1', 'cookie2=value2'],
            $headerManager->getHeader('Set-Cookie'));
        $this->assertEquals(
            'cookie1=value1, cookie2=value2',
            $headerManager->getHeaderLine('Set-Cookie')
        );
    }
}