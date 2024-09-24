<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Conditions;

use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;
use DJWeb\Framework\DBAL\Query\Builders\BaseQueryBuilder;

class WhereGroupCondition implements ConditionContract
{
    private BaseQueryBuilder $innerBuilder;

    public function __construct(
        BaseQueryBuilder $innerBuilder
    ) {
        $this->innerBuilder = clone $innerBuilder;
        $this->innerBuilder->clean();
    }

    public function andWhere(
        string $column,
        string $operator,
        mixed $value
    ): self {
        return $this->where($column, $operator, $value, true);
    }

    public function where(
        string $column,
        string $operator,
        mixed $value,
        bool $and = true
    ): self {
        $this->innerBuilder->where($column, $operator, $value, $and);
        return $this;
    }

    public function orWhere(
        string $column,
        string $operator,
        mixed $value
    ): self {
        return $this->where($column, $operator, $value, false);
    }

    public function whereLike(
        string $column,
        string $pattern,
        bool $and = true
    ): self {
        $this->innerBuilder->whereLike($column, $pattern, $and);
        return $this;
    }

    public function whereNull(string $column, bool $and = true): self
    {
        $this->innerBuilder->whereNull($column, $and);
        return $this;
    }

    public function whereNotNull(string $column, bool $and = true): self
    {
        $this->innerBuilder->whereNotNull($column, $and);
        return $this;
    }

    public function getSQL(): string
    {
        return '(' . substr($this->innerBuilder->buildWhereClause(), 6) . ')';
    }

    public function getParams(): array
    {
        return $this->innerBuilder->getParams();
    }
}
