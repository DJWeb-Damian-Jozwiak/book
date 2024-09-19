<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\TableManagerContract;
use DJWeb\Framework\DBAL\Schema\Column;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\Builders\AlterTableBuilder;
use DJWeb\Framework\DBAL\Schema\MySQL\Managers\Builders\CreateTableBuilder;
use DJWeb\Framework\Exceptions\DBAL\Schema\TableError;

readonly class TableManager implements TableManagerContract
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    /**
     * @param array<int, Column> $columns
     */
    public function createTable(string $tableName, array $columns): void
    {
        $sql = (new CreateTableBuilder())->build($tableName, $columns);
        $this->executeSql($sql, 'Failed to create table: ');
    }

    /**
     * @param array<int, mixed> $modifications
     */
    public function alterTable(string $tableName, array $modifications): void
    {
        $sql = (new AlterTableBuilder())->build($tableName, $modifications);
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
