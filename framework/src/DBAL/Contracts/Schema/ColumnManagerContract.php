<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

use DJWeb\Framework\DBAL\Schema\Column;

interface ColumnManagerContract
{
    public function addColumn(string $tableName, Column $column): void;
    public function modifyColumn(string $tableName, string $columnName, Column $newColumn): void;
    public function dropColumn(string $tableName, string $columnName): void;
}
