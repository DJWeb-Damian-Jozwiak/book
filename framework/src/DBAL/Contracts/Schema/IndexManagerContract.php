<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

interface IndexManagerContract
{
    /**
     * @param array<int, string> $columns
     */
    public function createIndex(
        string $tableName,
        string $indexName,
        array $columns
    ): void;

    /**
     * @param string|array<int, string> $columns
     */
    public function primary(string $tableName, string|array $columns): void;

    /**
     * @param string|array<int, string> $columns
     */
    public function unique(
        string $tableName,
        string $indexName,
        string|array $columns
    ): void;

    public function dropIndex(string $tableName, string $indexName): void;
}
