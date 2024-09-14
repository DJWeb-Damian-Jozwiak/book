<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

use ArrayObject;

/**
 * @phpstan-ignore-next-line
 */
class Headers extends ArrayObject
{
    /**
     * @param array<string, string> $server
     */
    public function __construct(array $server)
    {
        $headers = $this->parseHeaders($server);
        parent::__construct($headers, ArrayObject::ARRAY_AS_PROPS);
    }

    public static function empty(): Headers
    {
        return new Headers([]);
    }

    /**
     * @return array<array<string>>
     */
    public function getHeaders(): array
    {
        return (array) $this;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this[$name]);
    }

    /**
     * @return array<string>
     */
    public function getHeader(string $name): array
    {
        return isset($this[$name]) ? (array) $this[$name] : [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this[$name] ?? []);
    }

    /**
     * @param array<string>|string $value
     */
    public function withHeader(string $name, array|string $value): self
    {
        $clone = clone $this;
        $clone[$name] = (array) $value;
        return $clone;
    }

    /**
     * @param array<string>|string $value
     */
    public function withAddedHeader(string $name, array|string $value): self
    {
        $clone = clone $this;
        $clone[$name][] = $value;
        return $clone;
    }

    public function withoutHeader(string $name): self
    {
        $clone = clone $this;
        unset($clone[$name]);
        return $clone;
    }

    /**
     * @param array<string, string> $server
     *
     * @return array<int|string, string>
     */
    private function parseHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $header = str_replace(
                    ' ',
                    '-',
                    ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                );
                $headers[$header] = $value;
            } else {
                $headers[$name] = $value;
            }
        }
        return $headers;
    }
}
