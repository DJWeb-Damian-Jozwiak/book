<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Conditions;

use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;

class WhereCondition implements ConditionContract
{
    public function __construct(
        private string $column,
        private string $operator,
        private mixed $value
    ) {
    }

    public function getSQL(): string
    {
        return "{$this->column} {$this->operator} ?";
    }

    public function getParams(): array
    {
        return [$this->value];
    }
}
