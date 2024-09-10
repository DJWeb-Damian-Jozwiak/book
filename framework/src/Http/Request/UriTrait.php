<?php

namespace DJWeb\Framework\Http\Request;

use DJWeb\Framework\Http\Uri;
use Psr\Http\Message\UriInterface;

trait UriTrait
{
    private UriInterface $uri;

    public function buildUri(): void
    {
        $this->uri = new Uri(
            scheme: $this->server['REQUEST_SCHEME'],
            host: $this->server['SERVER_NAME'],
            port: $this->server['SERVER_PORT'],
            query: !empty($this->getParams) ? http_build_query($this->getParams) : ''
        );
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): self
    {
        $clone = clone $this;
        $clone->uri = $uri;
        return $clone;
    }

    public function getRequestTarget(): string
    {
        return $this->uri->getPath();
    }

}