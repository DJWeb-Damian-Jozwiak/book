<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class BaseRequest implements RequestInterface
{
    private string $protocolVersion = '1.1';

    public function __construct(
        protected string $method,
        protected UriInterface $uri,
        protected StreamInterface $body,
        protected HeaderManager $headerManager
    ) {
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): static
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headerManager->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->headerManager->hasHeader($name);
    }

    public function getHeader(string $name): array
    {
        return $this->headerManager->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->headerManager->getHeaderLine($name);
    }

    public function withHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->headerManager = $this->headerManager->withHeader($name, $value);
        return $new;
    }

    public function withAddedHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->headerManager = $this->headerManager->withAddedHeader(
            $name,
            $value
        );
        return $new;
    }

    public function withoutHeader(string $name): static
    {
        $new = clone $this;
        $new->headerManager = $this->headerManager->withoutHeader($name);
        return $new;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): static
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    public function getRequestTarget(): string
    {
        $target = $this->uri->getPath() . '/';

        $target .= '?' . $this->uri->getQuery();
        return rtrim($target, '?');
    }

    public function withRequestTarget(string $requestTarget): static
    {
        $new = clone $this;
        $new->uri = $new->uri->withPath($requestTarget);
        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): static
    {
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(
        UriInterface $uri,
        bool $preserveHost = false
    ): static {
        $new = clone $this;
        $new->uri = $uri;

        if (! $preserveHost) {
            $new = $new->updateHostFromUri();
        }

        return $new;
    }

    private function updateHostFromUri(): static
    {
        /** @phpstan-ignore-next-line */
        return UpdateHostFromUri::update($this, $this->uri);
    }
}
