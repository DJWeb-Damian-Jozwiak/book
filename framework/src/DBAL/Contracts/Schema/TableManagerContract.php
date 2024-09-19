<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Contracts\Schema;

interface TableManagerContract
{
    public function createTable(string $tableName, array $columns): void;

    public function alterTable(string $tableName, array $modifications): void;

    public function dropTable(string $tableName): void;
}
