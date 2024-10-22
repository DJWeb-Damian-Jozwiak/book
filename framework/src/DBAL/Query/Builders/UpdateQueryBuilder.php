<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

use DJWeb\Framework\DBAL\Contracts\Query\UpdateQueryBuilderContract;

class UpdateQueryBuilder extends BaseQueryBuilder implements UpdateQueryBuilderContract
{
    /**
     * @var array<string, int|float|string|null>
     */
    protected array $updates = [];

    /**
     * @param array<string, int|float|string|null> $updates
     *
     * @return $this
     */
    public function set(array $updates): self
    {
        $this->updates = $updates;
        return $this;
    }

    /**
     * @return array<int, float|int|string|null>
     */
    public function getParams(): array
    {
        return array_values($this->updates);
    }

    public function getSQL(): string
    {
        $setParts = [];
        foreach ($this->updates as $column => $value) {
            $setParts[] = "{$column} = ?";
            $this->params[] = $value;
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause} ";
        $sql .= $this->buildWhereClause();

        return $sql;
    }

    public function execute(): bool
    {
        /** @phpstan-ignore-next-line */
        return $this->connection->query($this->getSQL(), $this->getParams())
            ->execute();
    }
}
