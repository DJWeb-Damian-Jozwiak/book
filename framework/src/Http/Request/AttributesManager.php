<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

final class AttributesManager
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(public private(set) array $attributes = [])
    {
    }

    public function withAttribute(string $name, mixed $value): self
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute(string $name): self
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }
}
