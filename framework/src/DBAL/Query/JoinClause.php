<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query;

use DJWeb\Framework\DBAL\Contracts\Query\JoinClauseContract;

class JoinClause implements JoinClauseContract
{
    public function __construct(
        private string $type,
        private string $table,
        private string $first,
        private string $operator,
        private string $second
    ) {
    }

    public function getSQL(): string
    {
        return "{$this->type} {$this->table} ON {$this->first} {$this->operator} {$this->second}";
    }
}
