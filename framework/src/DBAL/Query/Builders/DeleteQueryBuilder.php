<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Builders;

class DeleteQueryBuilder extends BaseQueryBuilder
{
    public function getSQL(): string
    {
        $sql = "DELETE FROM {$this->table} ";
        $sql .= $this->buildWhereClause();
        return $sql;
    }
}
