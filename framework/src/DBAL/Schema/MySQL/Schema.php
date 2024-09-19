<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Schema\MySQL;

use DJWeb\Framework\DBAL\Contracts\Schema\ColumnFactoryContract;
use DJWeb\Framework\DBAL\Contracts\Schema\ColumnManagerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\DatabaseInfoContract;
use DJWeb\Framework\DBAL\Contracts\Schema\IndexManagerContract;
use DJWeb\Framework\DBAL\Contracts\Schema\SchemaContract;
use DJWeb\Framework\DBAL\Contracts\Schema\TableManagerContract;
use DJWeb\Framework\DBAL\Schema\Column;

readonly class Schema implements SchemaContract
{
    public function __construct(
        private TableManagerContract $tableManager,
        private ColumnManagerContract $columnManager,
        private IndexManagerContract $indexManager,
        private DatabaseInfoContract $databaseInfo,
        private ColumnFactoryContract $columnFactory,
    ) {
    }

    public function createTable(string $tableName, array $columns): void
    {
        $this->tableManager->createTable($tableName, $columns);
    }

    public function alterTable(string $tableName, array $modifications): void
    {
        $this->tableManager->alterTable($tableName, $modifications);
    }

    public function dropTable(string $tableName): void
    {
        $this->tableManager->dropTable($tableName);
    }

    public function addColumn(string $tableName, Column $column): void
    {
        $this->columnManager->addColumn($tableName, $column);
    }

    public function modifyColumn(
        string $tableName,
        string $columnName,
        Column $newColumn
    ): void {
        $this->columnManager->modifyColumn($tableName, $columnName, $newColumn);
    }

    public function dropColumn(string $tableName, string $columnName): void
    {
        $this->columnManager->dropColumn($tableName, $columnName);
    }

    public function createIndex(
        string $tableName,
        string $indexName,
        array $columns
    ): void {
        $this->indexManager->createIndex($tableName, $indexName, $columns);
    }

    public function dropIndex(string $tableName, string $indexName): void
    {
        $this->indexManager->dropIndex($tableName, $indexName);
    }

    public function getTables(): array
    {
        return $this->databaseInfo->getTables();
    }

    public function getColumns(string $tableName): array
    {
        return $this->databaseInfo->getColumns($tableName);
    }

    public function createFromDescription(array $description): Column
    {
        return $this->columnFactory->createFromDescription($description);
    }
}
