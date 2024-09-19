<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\ColumnManagerContract;
use DJWeb\Framework\DBAL\Schema\Column;
use DJWeb\Framework\Exceptions\DBAL\Schema\ColumnError;

readonly class ColumnManager implements ColumnManagerContract
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    public function addColumn(string $tableName, Column $column): void
    {
        $sql = "ALTER TABLE {$tableName} ADD COLUMN " . $column->getSqlDefinition(
        );
        $this->executeSql($sql, 'Failed to add column: ');
    }

    public function modifyColumn(
        string $tableName,
        string $columnName,
        Column $newColumn
    ): void {
        $sql = "ALTER TABLE {$tableName} CHANGE COLUMN {$columnName} " . $newColumn->getSqlDefinition(
        );
        $this->executeSql($sql, 'Failed to modify column: ');
    }

    public function dropColumn(string $tableName, string $columnName): void
    {
        $sql = "ALTER TABLE {$tableName} DROP COLUMN {$columnName}";
        $this->executeSql($sql, 'Failed to drop column: ');
    }

    private function executeSql(string $sql, string $msg): void
    {
        try {
            $this->connection->query($sql);
        } catch (\PDOException $e) {
            throw new ColumnError($msg . $e->getMessage());
        }
    }
}
