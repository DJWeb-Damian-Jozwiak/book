<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

use DJWeb\Framework\DBAL\Schema\Column;

interface DatabaseInfoContract
{
    /**
     * @return array<int, string>
     */
    public function getTables(): array;

    /**
     * @return array<int, Column>
     */
    public function getColumns(string $tableName): array;
}
