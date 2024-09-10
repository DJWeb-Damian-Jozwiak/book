<?php

namespace Tests\Http;

use DJWeb\Framework\Http\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function testToString()
    {
        // arrange
        $scheme = 'https';
        $userInfo = 'user:pass';
        $host = 'example.com';
        $port = 443;
        $path = '/path';
        $query = 'key=value';
        $fragment = 'section';
        //act
        $uri = new Uri($scheme, $userInfo, $host, $port, $path, $query, $fragment);
        // assert
        $expectedUrl = 'https://user:pass@example.com:443/path?key=value#section';
        // Sprawdzamy, czy metoda __toString zwraca poprawny URL
        $this->assertEquals($expectedUrl, (string)$uri);
    }

    public function testGetFragment()
    {
        $uri = new Uri();
        $this->assertEquals('', $uri->getFragment());
    }
    public function testWithFragment()
    {
        $uri = new Uri();
        $newUri = $uri->withFragment('section1');
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('section1', $newUri->getFragment());
    }

    public function testGetInvalidScheme()
    {
        $this->expectException(\InvalidArgumentException::class);
        $uri = new Uri(scheme: 'invalid');
    }
    public function testWithScheme()
    {
        $uri = new Uri(scheme: 'http');
        $this->assertEquals('http', $uri->getScheme());
        $newUri = $uri->withScheme('https');
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('https', $newUri->getScheme());
    }

    public function testGetHost()
    {
        $uri = new Uri();
        $this->assertEquals('', $uri->getHost());
    }
    public function testWithHost()
    {
        $uri = new Uri();
        $newUri = $uri->withHost('example.com');
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('example.com', $newUri->getHost());
    }

    public function testGetPath()
    {
        $uri = new Uri();
        $this->assertEquals('', $uri->getPath());
    }
    public function testWithPath()
    {
        $uri = new Uri();
        $newUri = $uri->withPath('/test');
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('/test', $newUri->getPath());
    }

    public function testGetPort()
    {
        $uri = new Uri();
        $this->assertEquals(80, $uri->getPort());
    }
    public function testWithPort()
    {
        $uri = new Uri();
        $newUri = $uri->withPort(8080);
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals(8080, $newUri->getPort());
    }

    public function testWithPortNull()
    {
        $uri = new Uri();
        $newUri = $uri->withPort(null);
        $this->assertNotSame($uri, $newUri);
        $this->assertNull($newUri->getPort());
    }
    public function testWithInvalidPort()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = new Uri();
        $uri->withPort(70000);
    }

    public function testGetQuery()
    {
        $uri = new Uri();
        $this->assertEquals('', $uri->getQuery());
    }
    public function testWithQuery()
    {
        $uri = new Uri();
        $newUri = $uri->withQuery('name=John&age=30');
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('name=John&age=30', $newUri->getQuery());
    }

    public function testWithQueryParams()
    {
        $uri = new Uri();
        $newUri = $uri->withQueryParams(['name' => 'John', 'age' => 30]);
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('name=John&age=30', $newUri->getQuery());
    }

    public function testWithUserInfo()
    {
        $uri = new Uri();
        $newUri = $uri->withUserInfo('john', 'doe');
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('john:doe', $newUri->getUserInfo());
    }
    public function testWithUserInfoWithoutPassword()
    {
        $uri = new Uri();
        $newUri = $uri->withUserInfo('john');
        $this->assertNotSame($uri, $newUri);
        $this->assertEquals('john', $newUri->getUserInfo());
    }

    public function testGetUserInfo()
    {
        $uri = new Uri();
        $this->assertEquals('', $uri->getUserInfo());
    }
}