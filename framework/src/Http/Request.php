<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Http\Request\BodyTrait;
use DJWeb\Framework\Http\Request\Headers;
use DJWeb\Framework\Http\Request\HeadersTrait;
use DJWeb\Framework\Http\Request\MethodTrait;
use DJWeb\Framework\Http\Request\ProtocolVersionTrait;
use DJWeb\Framework\Http\Request\UriTrait;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use ReflectionClass;

class Request implements RequestInterface
{
    use HeadersTrait;
    use BodyTrait;
    use MethodTrait;
    use UriTrait;
    use ProtocolVersionTrait;

    /**
     * @param array<string, string|int|float|bool|null> $getParams // $_GET
     * @param array<string, string|int|float|bool|null> $postParams // $_POST
     * @param array<string, string|int|float|bool|null> $cookies // $_COOKIE
     * @param array<string, array{name: string, type: string, tmp_name: string, error: int, size: int}|null> $files // $_FILES
     * @param array<string, string|int|array|string[]|null> $server // $_SERVER
     */
    private function __construct(
        public readonly array $getParams,
        public readonly array $postParams,
        public readonly array $cookies,
        public readonly array $files,
        public readonly array $server,
    ) {
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if (! $requestTarget) {
            throw new InvalidArgumentException(
                'Request target cannot be empty.'
            );
        }
        $uri = $this->uri->withPath($requestTarget);
        return $this->withUri($uri);
    }

    public static function createFromSuperglobals(): self
    {
        $request = (new Request(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER
        ));
        $request = $request->withMethod($request->server['REQUEST_METHOD'])
            ->withHeaders(new Headers($request->server));
        $request->buildUri();
        $request->loadBodyFromStream();
        return $request;
    }

    private function clone(
        RequestInterface $request,
        string $propertyName,
        mixed $propertyValue
    ): RequestInterface {
        $clone = clone $request;
        $reflection = new ReflectionClass($clone);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($clone, $propertyValue);
        return $clone;
    }
}
