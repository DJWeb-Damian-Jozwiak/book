<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\TableManagerContract;
use DJWeb\Framework\DBAL\Schema\Column;
use DJWeb\Framework\Exceptions\DBAL\Schema\TableError;

readonly class TableManager implements TableManagerContract
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    public function createTable(string $tableName, array $columns): void
    {
        $columnDefinitions = array_map(static function (Column $column) {
            return $column->getSqlDefinition();
        }, $columns);
        $sql = "CREATE TABLE {$tableName} (" . implode(
            ', ',
            $columnDefinitions
        ) . ')';
        $this->executeSql($sql, 'Failed to create table: ');
    }

    public function alterTable(string $tableName, array $modifications): void
    {
        $alterStatements = [];
        foreach ($modifications as $modification) {
            if ($modification instanceof Column) {
                $alterStatements[] = 'ADD COLUMN ' . $modification->getSqlDefinition(
                );
            } elseif (is_array($modification) && count($modification) === 2) {
                $alterStatements[] = "CHANGE COLUMN {$modification[0]} " . $modification[1]->getSqlDefinition(
                );
            } elseif (is_string($modification)) {
                $alterStatements[] = "DROP COLUMN {$modification}";
            }
        }

        $sql = "ALTER TABLE {$tableName} " . implode(', ', $alterStatements);
        $this->executeSql($sql, 'Failed to alter table: ');
    }

    public function dropTable(string $tableName): void
    {
        $sql = "DROP TABLE {$tableName}";
        $this->executeSql($sql, 'Failed to drop table: ');
    }

    private function executeSql(string $sql, string $msg): void
    {
        try {
            $this->connection->query($sql);
        } catch (\PDOException $e) {
            throw new TableError($msg . $e->getMessage());
        }
    }
}
