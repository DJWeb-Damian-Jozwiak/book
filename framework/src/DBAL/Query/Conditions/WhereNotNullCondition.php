<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Conditions;

use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;

class WhereNotNullCondition implements ConditionContract
{
    public function __construct(private string $column)
    {
    }

    public function getSQL(): string
    {
        return "{$this->column} IS NOT NULL";
    }

    public function getParams(): array
    {
        return [];
    }
}
