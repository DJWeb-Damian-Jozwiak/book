<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

final class QueryParamsManager
{
    /**
     * @param array<string, mixed> $queryParams
     */
    public function __construct(public private(set) array $queryParams = [])
    {
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return $this
     */
    public function withQueryParams(array $query): self
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    public function has(string $key): bool
    {
        return isset($this->queryParams[$key]);
    }
}
