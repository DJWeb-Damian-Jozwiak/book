<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        $uri = $uri instanceof UriInterface ? $uri : new UriManager($uri);
        return new Request(
            $method,
            $uri,
            new Stream('php://temp', 'r+'),
            new HeaderManager(),
        );
    }
}
