<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

use DJWeb\Framework\DBAL\Schema\Column;

interface SchemaContract
{
    public function createTable(string $tableName, array $columns): void;

    public function alterTable(string $tableName, array $modifications): void;

    public function dropTable(string $tableName): void;

    public function addColumn(string $tableName, Column $column): void;

    public function modifyColumn(
        string $tableName,
        string $columnName,
        Column $newColumn
    ): void;

    public function dropColumn(string $tableName, string $columnName): void;

    public function createIndex(
        string $tableName,
        string $indexName,
        array $columns
    ): void;

    public function dropIndex(string $tableName, string $indexName): void;

    public function getTables(): array;

    public function getColumns(string $tableName): array;

    public function createFromDescription(array $description): Column;
}
