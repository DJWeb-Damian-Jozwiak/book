<?php

namespace Tests\Http;

use DJWeb\Framework\Http\Uri\UriBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UriBuilderTest extends TestCase
{
    private UriBuilder $builder;
    private array $serverBackup;

    protected function setUp(): void
    {
        $this->builder = new UriBuilder();
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    #[DataProvider('schemeProvider')]
    public function testGetScheme(array $serverParams, string $expectedScheme): void
    {
        $_SERVER = $serverParams;

        $uri = $this->builder->createUriFromGlobals();

        $this->assertStringStartsWith($expectedScheme . '://', (string)$uri);
    }

    public static function schemeProvider(): array
    {
        return [
            'https with HTTPS server param' => [
                ['HTTPS' => 'on', 'SERVER_NAME' => 'example.com'],
                'https'
            ],
            'http with HTTPS off' => [
                ['HTTPS' => 'off', 'SERVER_NAME' => 'example.com'],
                'http'
            ],
            'https with forwarded proto' => [
                ['HTTP_X_FORWARDED_PROTO' => 'https', 'SERVER_NAME' => 'example.com'],
                'https'
            ],
            'default to http' => [
                ['SERVER_NAME' => 'example.com'],
                'http'
            ],
        ];
    }

    #[DataProvider('authorityProvider')]
    public function testGetAuthority(array $serverParams, string $expectedAuthority): void
    {
        $_SERVER = $serverParams;

        $uri = $this->builder->createUriFromGlobals();

        $this->assertStringContainsString($expectedAuthority, (string)$uri);
    }

    public static function authorityProvider(): array
    {
        return [
            'with HTTP_HOST' => [
                [
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '80',
                ],
                'example.com'
            ],
            'with SERVER_NAME' => [
                [
                    'SERVER_NAME' => 'example.org',
                    'SERVER_PORT' => '80',
                ],
                'example.org'
            ],
            'with non-standard HTTP port' => [
                [
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '8080',
                    'HTTPS' => 'off',
                ],
                'example.com:8080'
            ],
            'with non-standard HTTPS port' => [
                [
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '8443',
                    'HTTPS' => 'on',
                ],
                'example.com:8443'
            ],
            'with forwarded port' => [
                [
                    'HTTP_HOST' => 'example.com',
                    'HTTP_X_FORWARDED_PORT' => '8080',
                    'HTTPS' => 'off',
                ],
                'example.com:8080'
            ],
            'with standard HTTP port' => [
                [
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '80',
                    'HTTPS' => 'off',
                ],
                'example.com'
            ],
            'with standard HTTPS port' => [
                [
                    'HTTP_HOST' => 'example.com',
                    'SERVER_PORT' => '443',
                    'HTTPS' => 'on',
                ],
                'example.com'
            ],
        ];
    }

    #[DataProvider('pathProvider')]
    public function testGetPath(array $serverParams, string $expectedPath): void
    {
        $_SERVER = $serverParams;

        $uri = $this->builder->createUriFromGlobals();

        $this->assertStringContainsString($expectedPath, (string)$uri);
    }

    public static function pathProvider(): array
    {
        return [
            'root path' => [
                ['REQUEST_URI' => '/','HTTP_HOST' => 'example.com',],
                ''
            ],
            'simple path' => [
                ['REQUEST_URI' => '/path/to/resource','HTTP_HOST' => 'example.com'],
                '/path/to/resource'
            ],
            'path with query string' => [
                ['REQUEST_URI' => '/path?param=value','HTTP_HOST' => 'example.com'],
                '/path'
            ],
            'empty REQUEST_URI defaults to /' => [
                ['HTTP_HOST' => 'example.com'],
                '/'
            ],
        ];
    }

    #[DataProvider('queryProvider')]
    public function testGetQuery(array $serverParams, string $expectedQuery): void
    {
        $_SERVER = $serverParams;

        $uri = $this->builder->createUriFromGlobals();

        if (empty($expectedQuery)) {
            $this->assertStringNotContainsString('?', (string)$uri);
        } else {
            $this->assertStringEndsWith($expectedQuery, (string)$uri);
        }
    }

    public static function queryProvider(): array
    {
        return [
            'no query string' => [
                [],
                ''
            ],
            'empty query string' => [
                ['QUERY_STRING' => ''],
                ''
            ],
            'simple query' => [
                ['QUERY_STRING' => 'param=value','HTTP_HOST' => 'example.com'],
                '?param=value'
            ],
            'multiple parameters' => [
                ['QUERY_STRING' => 'param1=value1&param2=value2', 'HTTP_HOST' => 'example.com'],
                '?param1=value1&param2=value2'
            ],
        ];
    }

    public function testCompleteUri(): void
    {
        $_SERVER = [
            'HTTPS' => 'on',
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/path/to/resource',
            'QUERY_STRING' => 'param=value',
            'SERVER_PORT' => '443'
        ];

        $uri = $this->builder->createUriFromGlobals();

        $this->assertEquals(
            'https://example.com/path/to/resource?param=value',
            (string)$uri
        );
    }

    public function testCompleteUriWithNonStandardPort(): void
    {
        $_SERVER = [
            'HTTPS' => 'on',
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/path/to/resource',
            'QUERY_STRING' => 'param=value',
            'SERVER_PORT' => '8443'
        ];

        $uri = $this->builder->createUriFromGlobals();

        $this->assertEquals(
            'https://example.com:8443/path/to/resource?param=value',
            (string)$uri
        );
    }
}