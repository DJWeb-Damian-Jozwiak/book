<?php

namespace DJWeb\Framework\Http\Request;

trait HeadersTrait
{
    public Headers $headers;

    public function getHeaders(): array
    {
        return $this->headers->getHeaders();
    }

    public function withHeader($name, $value): self {
        $clone = clone $this;
        $clone->headers = $this->headers->withHeader($name, $value);
        return $clone;
    }

    public function withHeaders(Headers $headers): static
    {
        /** @phpstan-ignore-next-line */
        return $this->clone($this, 'headers', $headers);
    }
    public function withAddedHeader($name, $value): self {
        $clone = clone $this;
        $clone->headers = $this->headers->withAddedHeader($name, $value);
        return $clone;
    }
    public function withoutHeader($name): self {
        $clone = clone $this;
        $clone->headers = $this->headers->withoutHeader($name);
        return $clone;
    }
    public function hasHeader($name): bool {
        return $this->headers->hasHeader($name);
    }
    public function getHeader($name): array {
        return $this->headers->getHeader($name);
    }
    public function getHeaderLine($name): string {
        return $this->headers->getHeaderLine($name);
    }
}