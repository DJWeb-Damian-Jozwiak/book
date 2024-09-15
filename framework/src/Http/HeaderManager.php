<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

class HeaderManager
{
    private HeaderArray $headers;

    /**
     * @param array<string, string|array<int, string>> $headers
     */
    public function __construct(array $headers = [])
    {
        $this->headers = new HeaderArray($headers);
    }

    /**
     * @return array<array<string>>
     */
    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    /**
     * @return array<string>
     */
    public function getHeader(string $name): array
    {
        return $this->headers->get($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->headers->getLine($name);
    }

    /**
     * @param string|array<int, string> $value
     */
    public function withHeader(string $name, string|array $value): self
    {
        $new = new HeaderManager($this->headers->all());
        $new->headers->set($name, $value);
        return $new;
    }

    /**
     * @param string|array<int, string> $value
     */
    public function withAddedHeader(string $name, string|array $value): self
    {
        $new = new HeaderManager($this->headers->all());
        $new->headers->add($name, $value);
        return $new;
    }

    public function withoutHeader(string $name): self
    {
        $new = new HeaderManager($this->headers->all());
        $new->headers->remove($name);
        return $new;
    }
}
