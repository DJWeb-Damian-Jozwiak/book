<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface SelectQueryBuilderContract extends QueryBuilderContract
{
    /**
     * @param array<int, string> $columns
     *
     * @return self
     */
    public function select(array $columns = ['*']): self;

    public function limit(int $limit): self;

    public function offset(int $offset): self;

    public function leftJoin(string $table, string $first, string $operator, string $second): self;
    public function rightJoin(string $table, string $first, string $operator, string $second): self;
    public function innerJoin(string $table, string $first, string $operator, string $second): self;

    /**
     * @return array<string, mixed>|null
     */
    public function first(): ?array;

}
