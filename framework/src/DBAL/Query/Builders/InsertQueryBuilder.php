<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

class InsertQueryBuilder extends BaseQueryBuilder
{
    /**
     * @var array<int|string, int|string|float>
     */
    protected array $values = [];

    /**
     * @param array<int|string, int|string|float> $values
     */
    public function values(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function getSQL(): string
    {
        $columns = implode(', ', array_keys($this->values));
        $placeholders = implode(', ', array_fill(0, count($this->values), '?'));
        $this->params = array_values($this->values);

        return "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
    }
}
