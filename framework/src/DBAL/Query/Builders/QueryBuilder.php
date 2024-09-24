<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;

class QueryBuilder
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    public function insert(string $table): InsertQueryBuilder
    {
        return new InsertQueryBuilder($table, $this->connection);
    }

    public function update(string $table): UpdateQueryBuilder
    {
        return new UpdateQueryBuilder($table, $this->connection);
    }

    public function delete(string $table): DeleteQueryBuilder
    {
        return new DeleteQueryBuilder($table, $this->connection);
    }

    public function select(string $table): SelectQueryBuilder
    {
        return new SelectQueryBuilder($table, $this->connection);
    }
}
