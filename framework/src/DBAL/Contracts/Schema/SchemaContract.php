<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Schema\Column;

interface SchemaContract
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

    public function addColumn(string $tableName, Column $column): void;

    public function modifyColumn(
        string $tableName,
        string $columnName,
        Column $newColumn
    ): void;

    public function dropColumn(string $tableName, string $columnName): void;

    /**
     * @param array<int, string> $columns
     */
    public function createIndex(
        string $tableName,
        string $indexName,
        array $columns
    ): void;

    public function dropIndex(string $tableName, string $indexName): void;

    /**
     * @return array<int, string>
     */
    public function getTables(): array;

    /**
     * @param string $tableName
     *
     * @return array<int, Column>
     */
    public function getColumns(string $tableName): array;

    /**
     * @param array<string, mixed> $description
     */
    public function createFromDescription(array $description): Column;

    public function getTransaction(): TransactionContract;

    public function getConnection(): ConnectionContract;
}
