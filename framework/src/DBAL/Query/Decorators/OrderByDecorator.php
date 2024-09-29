<?php

namespace DJWeb\Framework\DBAL\Query\Decorators;

use DJWeb\Framework\DBAL\Contracts\Query\OrderByDecoratorContract;
use DJWeb\Framework\DBAL\Enums\OrderByDirection;

class OrderByDecorator implements OrderByDecoratorContract
{
    /**
     * @var array<int, string>
     */
    private array $columns = [];

    /**
     * @param string|array<int, string> $columns
     */
    public function orderBy(
        array|string $columns,
        OrderByDirection $direction = OrderByDirection::ASC
    ): OrderByDecoratorContract {
        $columns = $this->wrapColumns($columns);
        foreach ($columns as $column) {
            $this->columns[] = '`'.$column.'` ' . $direction->value;
        }
        return $this;
    }

    /**
     * @param string|array<int, string> $columns
     */
    public function orderByDesc(array|string $columns): OrderByDecoratorContract
    {
        return $this->orderBy($columns, OrderByDirection::DESC);
    }

    /**
     * @param string|array<int, string> $columns
     */
    public function orderByAsc(array|string $columns): OrderByDecoratorContract
    {
        return $this->orderBy($columns, OrderByDirection::ASC);
    }

    /**
     * @param array<int, string>|string $columns
     * @return array<int, string>
     */
    private function wrapColumns(array|string $columns): array
    {
        return is_array($columns) ? $columns : [$columns];
    }

    public function getSql(): string
    {
        $data =  'ORDER BY ' . implode(', ', $this->columns);
        return $this->columns ? $data : '';
    }
}