<?php

declare(strict_types=1);

namespace Tests\Http;


use DJWeb\Framework\Http\HeaderArray;
use PHPUnit\Framework\TestCase;

class HeaderArrayTest extends TestCase
{
    private HeaderArray $headerArray;

    protected function setUp(): void
    {
        $this->headerArray = new HeaderArray([
            'Content-Type' => 'application/json',
            'Accept' => ['text/html', 'application/xhtml+xml']
        ]);
    }

    public function testSet(): void
    {
        $this->headerArray->set('X-Custom-Header', 'Custom Value');
        $this->assertEquals(['Custom Value'],
            $this->headerArray->get('X-Custom-Header'));
    }

    public function testGet(): void
    {
        $this->assertEquals(['application/json'],
            $this->headerArray->get('Content-Type'));
        $this->assertEquals(['text/html', 'application/xhtml+xml'],
            $this->headerArray->get('Accept'));
    }

    public function testHas(): void
    {
        $this->assertTrue($this->headerArray->has('Content-Type'));
        $this->assertFalse($this->headerArray->has('X-Non-Existent'));
    }

    public function testRemove(): void
    {
        $this->headerArray->remove('Content-Type');
        $this->assertFalse($this->headerArray->has('Content-Type'));
    }

    public function testAdd(): void
    {
        $this->headerArray->add('Accept', 'application/xml');
        $this->assertEquals(
            ['text/html', 'application/xhtml+xml', 'application/xml'],
            $this->headerArray->get('Accept')
        );
    }

    public function testGetLine(): void
    {
        $this->assertEquals(
            'text/html, application/xhtml+xml',
            $this->headerArray->getLine('Accept')
        );
    }

    public function testAll(): void
    {
        $expected = [
            'Content-Type' => ['application/json'],
            'Accept' => ['text/html', 'application/xhtml+xml']
        ];
        $this->assertEquals($expected, $this->headerArray->all());
    }

    public function testArrayAccess(): void
    {
        $this->headerArray['X-Custom'] = ['Custom'];
        $this->assertTrue(isset($this->headerArray['X-Custom']));
        $this->assertEquals(['Custom'], $this->headerArray['X-Custom']);
        unset($this->headerArray['X-Custom']);
        $this->assertFalse(isset($this->headerArray['X-Custom']));
    }

    public function testIteratorAggregate(): void
    {
        $headers = iterator_to_array($this->headerArray);
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertArrayHasKey('Accept', $headers);
    }

    public function testCountable(): void
    {
        $this->assertEquals(2, count($this->headerArray));
    }
}