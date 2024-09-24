<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Conditions;

use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;

class WhereLikeCondition implements ConditionContract
{
    public function __construct(
        private string $column,
        private string $pattern
    ) {
    }

    public function getSQL(): string
    {
        return "{$this->column} LIKE ?";
    }

    public function getParams(): array
    {
        return [$this->pattern];
    }
}
