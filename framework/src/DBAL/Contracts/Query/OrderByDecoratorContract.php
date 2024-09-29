<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

use DJWeb\Framework\DBAL\Enums\OrderByDirection;

interface OrderByDecoratorContract
{
    /**
     * @param string|array<int, string> $columns
     */
    public function orderBy(
        string|array $columns,
        OrderByDirection $direction = OrderByDirection::ASC
    ): self;

    /**
     * @param string|array<int, string> $columns
     */
    public function orderByDesc(string|array $columns): self;
    /**
     * @param string|array<int, string> $columns
     */
    public function orderByAsc(string|array $columns): self;

    public function getSql(): string;
}
