<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

interface IndexManagerContract
{
    public function createIndex(string $tableName, string $indexName, array $columns): void;
    public function dropIndex(string $tableName, string $indexName): void;
}
