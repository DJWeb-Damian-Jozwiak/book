<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request\Psr17;

use DJWeb\Framework\Http\HeaderManager;
use DJWeb\Framework\Http\Request\AttributesManager;
use DJWeb\Framework\Http\Request\ParsedBody;
use DJWeb\Framework\Http\Request\ParsedBodyManager;
use DJWeb\Framework\Http\Request\Psr7\Request;
use DJWeb\Framework\Http\Request\QueryParamsManager;
use DJWeb\Framework\Http\Request\UploadedFilesManager;
use DJWeb\Framework\Http\Stream;
use DJWeb\Framework\Http\UploadedFile\UploadedFileFactory;
use DJWeb\Framework\Http\Uri\UriBuilder;
use DJWeb\Framework\Http\UriManager;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class RequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @param string $method
     * @param $uri
     * @param array<string, mixed> $serverParams
     *
     * @return Request
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): Request
    {
        $uri = $uri instanceof UriInterface ? $uri : new UriManager($uri);
        return new Request(
            $method,
            $uri,
            new Stream('php://temp'),
            new HeaderManager([]),
            $serverParams,
            [],
            new QueryParamsManager([]),
            new UploadedFilesManager([]),
            new ParsedBodyManager(null),
            new AttributesManager()
        );
    }

    public function createFromGlobals(): Request
    {
        return new Request(
            ...$this->getRequestConstructorParams()
        );
    }

    /**
     * @return array<int, mixed>
     * @throws \JsonException
     */
    public function getRequestConstructorParams(): array
    {
        return [
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            new UriBuilder()->createUriFromGlobals(),
            new Stream('php://input'),
            new HeaderManager($this->getAllHeaders()),
            $_SERVER,
            $_COOKIE,
            new QueryParamsManager($_GET),
            new UploadedFilesManager($this->parseFiles($_FILES)),
            new ParsedBodyManager(new ParsedBody()->get()),
            new AttributesManager(),
        ];
    }

    /**
     * @param array<string, mixed> $files
     *
     * @return array<string, UploadedFileInterface>
     */
    private function parseFiles(array $files): array
    {
        $parsedFiles = [];
        $files = array_filter($files, static fn (mixed $file) => is_array($file));
        foreach ($files as $key => $file) {
            $parsedFiles[$key] = UploadedFileFactory::createUploadedFile(
                $file['name'],
                $file['size'],
                $file['error'],
                $file['tmp_name'],
                $file['type'] ?? null
            );
        }
        return $parsedFiles;
    }

    /**
     * @return array<string, string>
     */
    private function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $headers[
                    str_replace(
                        ' ',
                        '-',
                        ucwords(
                            strtolower(str_replace('_', ' ', substr($name, 5)))
                        )
                    )
                ] = $value;
            }
        }
        return $headers;
    }
}
