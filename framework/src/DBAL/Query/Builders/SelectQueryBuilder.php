<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\LimitDecoratorContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Query\Decorators\InnerJoinDecorator;
use DJWeb\Framework\DBAL\Query\Decorators\JoinDecorator;
use DJWeb\Framework\DBAL\Query\Decorators\LeftJoinDecorator;
use DJWeb\Framework\DBAL\Query\Decorators\LimitDecorator;
use DJWeb\Framework\DBAL\Query\Decorators\OrderByDecorator;
use DJWeb\Framework\DBAL\Query\Decorators\RightJoinDecorator;

class SelectQueryBuilder extends BaseQueryBuilder implements SelectQueryBuilderContract
{
    /**
     * @var array<int, string>
     */
    protected array $columns = ['*'];
    /** @var array<int, JoinDecorator> */
    protected array $joins = [];
    protected LimitDecoratorContract $limitDecorator;
    protected ?int $offset = null;
    protected OrderByDecorator $orderByDecorator;

    public function __construct(ConnectionContract $connection)
    {
        parent::__construct($connection);
        $this->limitDecorator = new LimitDecorator();
        $this->orderByDecorator = new OrderByDecorator();
    }

    /**
     * @param array<int, string> $columns
     *
     * @return $this
     */
    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->limitDecorator->offset($offset);
        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function first(): ?array
    {
        $result = $this->limit(1)->get();
        return $result[0] ?? null;
    }

    public function limit(int $limit): self
    {
        $this->limitDecorator->limit($limit);
        return $this;
    }

    public function orderBy(string $column): static
    {
        $this->orderByDecorator->orderByAsc($column);
        return $this;
    }

    public function orderByDesc(string $column): static
    {
        $this->orderByDecorator->orderByDesc($column);
        return $this;
    }

    public function getSQL(): string
    {
        $sql = 'SELECT ' . implode(
            ', ',
            $this->columns
        ) . " FROM {$this->table} ";

        foreach ($this->joins as $join) {
            $sql .= $join->getSQL() . ' ';
        }

        $sql .= $this->buildWhereClause();
        $sql .= $this->orderByDecorator->getSQL();
        $sql .= ' '. $this->limitDecorator->getSQL();

        return trim($sql);
    }

    public function leftJoin(
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {
        $this->joins[] = (new LeftJoinDecorator($this))->join(
            $table,
            $first,
            $operator,
            $second
        );
        return $this;
    }

    public function rightJoin(
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {
        $this->joins[] = (new RightJoinDecorator($this))->join(
            $table,
            $first,
            $operator,
            $second
        );
        return $this;
    }

    public function innerJoin(
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {
        $this->joins[] = (new InnerJoinDecorator($this))->join(
            $table,
            $first,
            $operator,
            $second
        );
        return $this;
    }
}
