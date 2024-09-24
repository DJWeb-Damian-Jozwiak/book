<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Conditions;

use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;

class OrCondition implements ConditionContract
{
    public function __construct(private ConditionContract $condition)
    {
    }

    public function getSQL(): string
    {
        return 'OR ' . $this->condition->getSQL();
    }

    /**
     * @return array<int, int|string|float|null>
     */
    public function getParams(): array
    {
        return $this->condition->getParams();
    }
}
