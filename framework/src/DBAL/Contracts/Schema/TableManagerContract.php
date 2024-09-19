<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

use DJWeb\Framework\DBAL\Schema\Column;

interface TableManagerContract
{
    /**
     * @param array<int, Column> $columns
     */
    public function createTable(string $tableName, array $columns): void;

    /**
     * @param array<int, mixed> $modifications
     */
    public function alterTable(string $tableName, array $modifications): void;

    public function dropTable(string $tableName): void;
}
