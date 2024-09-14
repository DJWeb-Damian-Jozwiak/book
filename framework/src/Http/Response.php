<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use DJWeb\Framework\Http\Request\BodyTrait;
use DJWeb\Framework\Http\Request\HeadersTrait;
use DJWeb\Framework\Http\Request\ProtocolVersionTrait;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

class Response implements ResponseInterface
{
    use HeadersTrait;
    use BodyTrait;
    use ProtocolVersionTrait;
    public function __construct(
        private int $statusCode = 200,
        private string $reasonPhrase = '',
    ) {
        $this->body = new Stream();
    }

    public function setContent(string $content): ResponseInterface
    {
        $this->body->write($content);
        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = ''): static
    {
        $cloned = $this->clone($this, 'statusCode', $code);
        /** @phpstan-ignore return.type */
        return $this->clone($cloned, 'reasonPhrase', $reasonPhrase);
    }

    private function clone(
        ResponseInterface $request,
        string $propertyName,
        mixed $propertyValue
    ): ResponseInterface {
        $clone = clone $request;
        $reflection = new ReflectionClass($clone);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($clone, $propertyValue);
        return $clone;
    }
}
