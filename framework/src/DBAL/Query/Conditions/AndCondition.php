<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Query\Conditions;

use DJWeb\Framework\DBAL\Contracts\Query\ConditionContract;

class AndCondition implements ConditionContract
{
    public function __construct(
        private ConditionContract $condition,
        private int $conditionCount = 0
    ) {
    }

    public function getSQL(): string
    {
        $prefix = $this->conditionCount === 0 ? '' : 'AND ';
        return $prefix . $this->condition->getSQL();
    }

    /**
     * @return array<int, int|string|float|null>
     */
    public function getParams(): array
    {
        return $this->condition->getParams();
    }
}
