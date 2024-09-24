<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Decorators;

use DJWeb\Framework\DBAL\Contracts\Query\JoinClauseContract;
use DJWeb\Framework\DBAL\Query\Builders\BaseQueryBuilder;
use DJWeb\Framework\DBAL\Query\JoinClause;

abstract class JoinDecorator implements JoinClauseContract
{
    protected BaseQueryBuilder $queryBuilder;
    /**
     * @var array<int, JoinClause>
     */
    protected array $joins = [];

    public function __construct(BaseQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = clone $queryBuilder;
        $this->queryBuilder->clean();
    }

    public function join(
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {
        $this->joins[] = new JoinClause(
            $this->joinType(),
            $table,
            $first,
            $operator,
            $second
        );
        return $this;
    }

    public function getSQL(): string
    {
        $sql = $this->queryBuilder->getSQL();
        foreach ($this->joins as $join) {
            $sql .= ' ' . $join->getSQL();
        }
        return $sql;
    }

    abstract protected function joinType(): string;
}
