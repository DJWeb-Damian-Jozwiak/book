<?php

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface SelectQueryBuilderContract extends QueryBuilderContract
{
    /**
     * @param array<int, string> $columns
     *
     * @return $this
     */
    public function select(array $columns = ['*']): self;
    public function limit(int $limit): self;
    public function offset(int $offset): self;
    /**
     * @return array<string, mixed>|null
     */
    public function first(): ?array;
}