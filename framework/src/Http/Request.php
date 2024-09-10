<?php

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
     * @param array<string, mixed> $getParams  // $_GET
     * @param array<string, mixed> $postParams // $_POST
     * @param array<string, mixed> $cookies    // $_COOKIE
     * @param array<string, mixed> $files      // $_FILES
     * @param array<string, mixed> $server     // $_SERVER
     */
    private function __construct(
        public readonly array $getParams,
        public readonly array $postParams,
        public readonly array $cookies,
        public readonly array $files,
        public readonly array $server,
    ) {}

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if (empty($requestTarget)) {
            throw new InvalidArgumentException('Request target cannot be empty.');
        }
        $uri = $this->uri->withPath($requestTarget);
        return $this->withUri($uri);
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
    public static function createFromSuperglobals(): self {
        $request = (new Request(
            $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER
        ));
        $request = $request->withMethod($request->server['REQUEST_METHOD'])
            ->withHeaders(new Headers($request->server));
        $request->buildUri();
        $request->loadBodyFromStream();
        return $request;
    }
}