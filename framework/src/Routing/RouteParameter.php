<?php

declare(strict_types=1);

namespace DJWeb\Framework\Routing;

readonly class RouteParameter
{
    final public function __construct(
        public string $name,
        public string $pattern = '[\d\p{L}-]+',
    ) {
    }

    /**
     * @param array<string, mixed> $matches
     *
     * @return mixed
     */
    public function getValue(array $matches): mixed
    {
        return $matches[$this->name] ?? null;
    }
}
