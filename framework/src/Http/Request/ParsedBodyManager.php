<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

final class ParsedBodyManager
{
    /**
     * @param ?array<string, mixed> $parsedBody
     */
    public function __construct(public private(set) ?array $parsedBody = null)
    {
    }

    /**
     * @param ?array<string, mixed> $data
     *
     * @return $this
     */
    public function withParsedBody(?array $data): self
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    public function has(string $key): bool
    {
        return isset($this->parsedBody[$key]);
    }
}
