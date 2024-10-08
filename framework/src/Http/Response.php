<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    private string $reasonPhrase = '';
    private HeaderManager $headers;
    private StreamInterface $body;

    /**
     * @param array<string, string|array<int, string>> $headers
     */
    public function __construct(
        array $headers = [],
        ?StreamInterface $body = null,
        private string $version = '1.1',
        private int $status = 200,
        ?string $reason = null
    ) {
        $this->headers = new HeaderManager($headers);
        $this->body = $body ?? $this->createDefaultBody();
        $this->reasonPhrase = $reason ?? $this->getDefaultReasonPhrase($status);
    }

    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    public function withProtocolVersion(string $version): static
    {
        $new = clone $this;
        $new->version = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->headers->hasHeader($name);
    }

    public function getHeader(string $name): array
    {
        return $this->headers->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->headers->getHeaderLine($name);
    }

    public function withHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->headers = $this->headers->withHeader($name, $value);
        return $new;
    }

    public function withAddedHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->headers = $this->headers->withAddedHeader($name, $value);
        return $new;
    }

    public function withoutHeader(string $name): static
    {
        $new = clone $this;
        $new->headers = $this->headers->withoutHeader($name);
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

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        $new = clone $this;
        $new->status = $code;
        $new->reasonPhrase =
            $reasonPhrase ? $reasonPhrase
                : $this->getDefaultReasonPhrase($code);
        return $new;
    }

    public function withContent(string $content): ResponseInterface
    {
        $this->body->write($content);
        return $this;
    }

    /**
     * @param array<int|string, mixed> $data
     */
    public function withJson(
        array $data,
        int $status = 200,
        int $options = JSON_THROW_ON_ERROR
    ): ResponseInterface {
        $json = json_encode($data, $options | JSON_THROW_ON_ERROR);

        return $this
            ->withHeader('Content-Type', 'application/json')
            ->withContent($json)
            ->withStatus($status);
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function redirect(string $url, int $status = 302): self
    {
        return $this
            ->withStatus($status)
            ->withHeader('Location', $url);
    }

    private function createDefaultBody(): StreamInterface
    {
        return new Stream('php://temp', 'r+');
    }

    private function getDefaultReasonPhrase(int $statusCode): string
    {
        $phrases = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];

        return $phrases[$statusCode] ?? '';
    }
}
