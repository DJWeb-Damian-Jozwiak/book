<?php

namespace DJWeb\Framework\Http\Uri;

trait QueryTrait
{
    private string $query = '';
    public function getQuery(): string
    {
        return $this->query;
    }
    public function withQuery(string $query): self
    {
        return $this->clone($this, 'query', $query);
    }

    /**
     * @param array<string, string|int|float> $params
     * @return \DJWeb\Framework\Http\Uri
     */
    public function withQueryParams(array $params): self
    {
        return $this->clone($this, 'query', http_build_query($params));
    }
}