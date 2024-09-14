<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

use DJWeb\Framework\Http\Stream;
use Psr\Http\Message\StreamInterface;

trait BodyTrait
{
    private StreamInterface $body;
    public function getBody(): StreamInterface
    {
        return $this->body;
    }
    public function withBody(StreamInterface $body): self
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getParams[$key] ?? $default;
    }
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->postParams[$key] ?? $default;
    }

    public function json(string $key, mixed $default = null): mixed
    {
        $body = $this->getBody()->getContents();
        if (! json_validate($body)) {
            throw new \RuntimeException('Invalid JSON body');
        }
        $jsonData = json_decode($body, true);
        return $jsonData[$key] ?? $default;
    }

    private function loadBodyFromStream(): void
    {
        $this->body = new Stream(fopen('php://input', 'r'));
    }
}
