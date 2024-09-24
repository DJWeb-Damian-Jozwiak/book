<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Query;

interface QueryBuilderContract
{
    public function getSql(): string;

    /**
     * @return array<int, int|string|float|null>
     */
    public function getParams(): array;
}
