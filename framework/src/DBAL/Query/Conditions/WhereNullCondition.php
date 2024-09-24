<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Conditions;

use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;

class WhereNullCondition implements ConditionContract
{
    public function __construct(private string $column)
    {
    }

    public function getSQL(): string
    {
        return "{$this->column} IS NULL";
    }

    public function getParams(): array
    {
        return [];
    }
}
