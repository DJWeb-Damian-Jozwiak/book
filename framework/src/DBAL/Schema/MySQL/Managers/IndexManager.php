<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL\Managers;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Schema\IndexManagerContract;
use DJWeb\Framework\Exceptions\DBAL\Schema\IndexError;

readonly class IndexManager implements IndexManagerContract
{
    public function __construct(private ConnectionContract $connection)
    {
    }

    /**
     * @param array<int, string> $columns
     */
    public function createIndex(
        string $tableName,
        string $indexName,
        array $columns
    ): void {
        $columnList = implode(', ', $columns);
        $sql = "CREATE INDEX {$indexName} ON {$tableName} ({$columnList})";
        $this->executeSql($sql, 'Failed to create index: ');
    }

    public function dropIndex(string $tableName, string $indexName): void
    {
        $sql = "DROP INDEX {$indexName} ON {$tableName}";
        $this->executeSql($sql, 'Failed to drop index: ');
    }

    /**
     * @param array<int, string> $columns
     */
    public function primary(string $tableName, array $columns): void
    {
        $columnList = implode(', ', $columns);
        $sql = "ALTER TABLE {$tableName} ADD PRIMARY KEY ({$columnList})";
        $this->executeSql($sql, 'Failed to create primary key: ');
    }

    /**
     * @param array<int, string> $columns
     */
    public function unique(
        string $tableName,
        string $indexName,
        array $columns
    ): void {
        $columnList = implode(', ', $columns);
        $sql = "CREATE UNIQUE INDEX {$indexName} ON {$tableName} ({$columnList})";
        $this->executeSql($sql, 'Failed to create unique index: ');
    }

    private function executeSql(string $sql, string $msg): void
    {
        try {
            $this->connection->query($sql);
        } catch (\PDOException $e) {
            throw new IndexError($msg . $e->getMessage());
        }
    }
}
