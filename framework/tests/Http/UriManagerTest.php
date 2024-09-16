<?php

declare(strict_types=1);

namespace Http;

use DJWeb\Framework\Http\UriManager;
use DJWeb\Framework\Http\UriStringBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriManagerTest extends TestCase
{
    public function testFullUriManipulation(): void
    {
        $uri = new UriManager(
            'https://user:pass@example.com:8080/path?query=value#fragment'
        );

        // Test getters
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/path', $uri->getPath());
        $this->assertEquals('query=value', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
        $this->assertEquals('user:pass@example.com:8080', $uri->getAuthority());

        // Test withers
        $newUri = $uri
            ->withScheme('http')
            ->withUserInfo('newuser', 'newpass')
            ->withHost('newexample.com')
            ->withPort(9090)
            ->withPath('/newpath')
            ->withQuery('newquery=newvalue')
            ->withFragment('newfragment');

        $this->assertEquals('http', $newUri->getScheme());
        $this->assertEquals('newuser:newpass', $newUri->getUserInfo());
        $this->assertEquals('newexample.com', $newUri->getHost());
        $this->assertEquals(9090, $newUri->getPort());
        $this->assertEquals('/newpath', $newUri->getPath());
        $this->assertEquals('newquery=newvalue', $newUri->getQuery());
        $this->assertEquals('newfragment', $newUri->getFragment());

        // Test __toString
        $this->assertEquals(
            'http://newuser:newpass@newexample.com:9090/newpath?newquery=newvalue#newfragment',
            (string)$newUri
        );

        // Test that original URI is unchanged
        $this->assertEquals(
            'https://user:pass@example.com:8080/path?query=value#fragment',
            (string)$uri
        );
    }

    public function testPartialUriManipulation(): void
    {
        $uri = new UriManager();

        $newUri = $uri
            ->withScheme('https')
            ->withHost('example.com')
            ->withPath('/api/v1')
            ->withQuery('sort=desc&limit=10');

        $this->assertEquals(
            'https://example.com/api/v1?sort=desc&limit=10',
            (string)$newUri
        );
    }

    public function testAuthorityManipulation(): void
    {
        $uri = new UriManager('https://example.com');

        $newUri = $uri->withUserInfo('user', 'pass')->withPort(8443);

        $this->assertEquals(
            'user:pass@example.com:8443',
            $newUri->getAuthority()
        );
        $this->assertEquals(
            'https://user:pass@example.com:8443',
            (string)$newUri
        );
    }

    public function testQueryAndFragmentManipulation(): void
    {
        $uri = new UriManager('https://example.com/path');

        $newUri = $uri->withQuery('key1=value1&key2=value2')->withFragment(
            'section1'
        );

        $this->assertEquals('key1=value1&key2=value2', $newUri->getQuery());
        $this->assertEquals('section1', $newUri->getFragment());
        $this->assertEquals(
            'https://example.com/path?key1=value1&key2=value2#section1',
            (string)$newUri
        );
    }

    public function testEmptyComponents(): void
    {
        $uri = new UriManager();

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
        $this->assertEquals('', (string)$uri);
    }

    public function testSpecialCharacters(): void
    {
        $uri = new UriManager();

        $newUri = $uri
            ->withScheme('https')
            ->withUserInfo('user@example.com', 'pass/word')
            ->withHost('exam!ple.com')
            ->withPath('/path with spaces')
            ->withQuery('key=value with spaces')
            ->withFragment('frag#ment');

        $expectedString = 'https://user%40example.com:pass%2Fword@exam!ple.com/path%20with%20spaces?key=value%20with%20spaces#frag%23ment';
        $this->assertEquals($expectedString, (string)$newUri);
    }

    public function testWithQueryEncodesValues(): void
    {
        $uri = new UriManager('https://example.com');

        $newUri = $uri->withQuery('key1=value 1&key2=value@2&key3=value/3');

        $this->assertEquals(
            'key1=value%201&key2=value%402&key3=value%2F3',
            $newUri->getQuery()
        );
        $this->assertEquals(
            'https://example.com?key1=value%201&key2=value%402&key3=value%2F3',
            (string)$newUri
        );
    }

    public function testWithQueryHandlesEmptyQuery(): void
    {
        $uri = new UriManager('https://example.com?existingquery=value');

        $newUri = $uri->withQuery('');

        $this->assertEquals('', $newUri->getQuery());
        $this->assertEquals('https://example.com', (string)$newUri);
    }

    public function testWithQueryHandlesQueryWithoutValues(): void
    {
        $uri = new UriManager('https://example.com');

        $newUri = $uri->withQuery('key1&key2&key3');

        $this->assertEquals('key1&key2&key3', $newUri->getQuery());
        $this->assertEquals(
            'https://example.com?key1&key2&key3',
            (string)$newUri
        );
    }

    public function testBuildWithPathStartingWithSlashAndAuthority(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn('https');
        $uri->method('getAuthority')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/path');
        $uri->method('getQuery')->willReturn('');
        $uri->method('getFragment')->willReturn('');

        $result = UriStringBuilder::build($uri);
        $this->assertEquals('https://example.com/path', $result);
    }

    public function testBuildWithPathNotStartingWithSlashAndAuthority(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn('https');
        $uri->method('getAuthority')->willReturn('example.com');
        $uri->method('getPath')->willReturn('path');
        $uri->method('getQuery')->willReturn('');
        $uri->method('getFragment')->willReturn('');

        $result = UriStringBuilder::build($uri);
        $this->assertEquals('https://example.com/path', $result);
    }

    public function testBuildWithEmptyPathAndAuthority(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn('https');
        $uri->method('getAuthority')->willReturn('example.com');
        $uri->method('getPath')->willReturn('');
        $uri->method('getQuery')->willReturn('');
        $uri->method('getFragment')->willReturn('');

        $result = UriStringBuilder::build($uri);
        $this->assertEquals('https://example.com', $result);
    }

    public function testBuildWithRootPathAndNoAuthority(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn('');
        $uri->method('getAuthority')->willReturn('');
        $uri->method('getPath')->willReturn('/');
        $uri->method('getQuery')->willReturn('');
        $uri->method('getFragment')->willReturn('');

        $result = UriStringBuilder::build($uri);
        $this->assertEquals('', $result);
    }

    public function testBuildWithNonRootPathAndNoAuthority(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn('');
        $uri->method('getAuthority')->willReturn('');
        $uri->method('getPath')->willReturn('/path');
        $uri->method('getQuery')->willReturn('');
        $uri->method('getFragment')->willReturn('');

        $result = UriStringBuilder::build($uri);
        $this->assertEquals('/path', $result);
    }

    public function testBuildWithQueryAndFragment(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn('https');
        $uri->method('getAuthority')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/path');
        $uri->method('getQuery')->willReturn('key=value');
        $uri->method('getFragment')->willReturn('fragment');

        $result = UriStringBuilder::build($uri);
        $this->assertEquals(
            'https://example.com/path?key=value#fragment',
            $result
        );
    }

    public function testWithQueryPreservesEncodedValues(): void
    {
        $uri = new UriManager('https://example.com');

        $newUri = $uri->withQuery(
            'already%20encoded=value%20here&normal=not encoded'
        );

        $this->assertEquals(
            'already%2520encoded=value%2520here&normal=not%20encoded',
            $newUri->getQuery()
        );
    }
}