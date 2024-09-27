<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface QueryBuilderContract
{
    public function getSql(): string;

    /**
     * @return array<int, int|string|float|null>
     */
    public function getParams(): array;

    public function table(string $table): static;

    public function clean(): void;

    public function andWhere(
        string $column,
        string $operator,
        mixed $value
    ): static;

    public function where(
        string $column,
        string $operator,
        mixed $value,
        bool $and = true
    ): static;

    public function orWhere(
        string $column,
        string $operator,
        mixed $value
    ): static;

    public function whereLike(
        string $column,
        string $pattern,
        bool $and = true
    ): static;

    public function whereNull(string $column, bool $and = true): self;
    public function whereNotNull(string $column, bool $and = true): self;
    public function whereGroup(callable $callback, bool $and = true): self;
    public function buildWhereClause(): string;
    /**
     * @return array<int, array<string, mixed>>
     */
    public function get(): array;
}
