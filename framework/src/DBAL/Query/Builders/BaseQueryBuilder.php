<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;
use DJWeb\Framework\DBAL\Contracts\Query\QueryBuilderContract;
use DJWeb\Framework\DBAL\Query\Conditions\AndCondition;
use DJWeb\Framework\DBAL\Query\Conditions\OrCondition;
use DJWeb\Framework\DBAL\Query\Conditions\WhereCondition;
use DJWeb\Framework\DBAL\Query\Conditions\WhereGroupCondition;
use DJWeb\Framework\DBAL\Query\Conditions\WhereLikeCondition;
use DJWeb\Framework\DBAL\Query\Conditions\WhereNotNullCondition;
use DJWeb\Framework\DBAL\Query\Conditions\WhereNullCondition;

abstract class BaseQueryBuilder implements QueryBuilderContract
{
    /**
     * @var array<int, ConditionContract>
     */
    protected array $conditions = [];
    protected string $table;
    /**
     * @var array<int, int|string|float>
     */
    protected array $params = [];

    public function __construct(
        protected ConnectionContract $connection
    ) {
    }

    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function clean(): void
    {
        $this->conditions = [];
        $this->params = [];
    }

    public function andWhere(
        string $column,
        string $operator,
        mixed $value
    ): static {
        return $this->where($column, $operator, $value, true);
    }

    public function where(
        string $column,
        string $operator,
        mixed $value,
        bool $and = true
    ): static {
        $condition = new WhereCondition($column, $operator, $value);
        $item = $and ?
            new AndCondition($condition, count($this->conditions))
            : new OrCondition($condition);
        $this->conditions[] = $item;
        return $this;
    }

    public function orWhere(
        string $column,
        string $operator,
        mixed $value
    ): static {
        return $this->where($column, $operator, $value, false);
    }

    public function whereLike(
        string $column,
        string $pattern,
        bool $and = true
    ): static {
        $condition = new WhereLikeCondition($column, $pattern);
        return $this->whereCondition($condition, $and);
    }

    public function whereNull(string $column, bool $and = true): self
    {
        $condition = new WhereNullCondition($column);
        return $this->whereCondition($condition, $and);
    }

    public function whereNotNull(string $column, bool $and = true): self
    {
        $condition = new WhereNotNullCondition($column);
        return $this->whereCondition($condition, $and);
    }

    public function whereGroup(callable $callback, bool $and = true): self
    {
        $groupCondition = new WhereGroupCondition($this);
        $callback($groupCondition);
        return $this->whereCondition($groupCondition, $and);
    }

    public function buildWhereClause(): string
    {
        if (! $this->conditions) {
            return '';
        }
        return 'WHERE ' . implode(
            ' ',
            array_map(
                static fn (
                    ConditionContract $condition
                ) => $condition->getSQL(),
                $this->conditions
            )
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get(): array
    {
        $statement = $this->connection->query(
            $this->getSQL(),
            $this->getParams()
        );
        /** @phpstan-ignore-next-line */
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, int|string|float|null>
     */
    public function getParams(): array
    {
        return array_merge(
            ...array_map(
                static fn (
                    ConditionContract $condition
                ) => $condition->getParams(),
                $this->conditions
            )
        );
    }

    private function whereCondition(
        ConditionContract $condition,
        bool $and = true
    ): static {
        $item = $and ?
            new AndCondition($condition, count($this->conditions))
            : new OrCondition($condition);
        $this->conditions[] = $item;
        return $this;
    }
}
